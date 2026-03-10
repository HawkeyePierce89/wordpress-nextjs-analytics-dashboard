<?php
/**
 * WP-CLI seed command for the Analytics Dashboard plugin.
 *
 * Usage:
 *   wp seed generate
 *
 * Creates 5 authors, 6 categories, 40 posts (with ACF metadata), and
 * 18 activity events in the wp_dashboard_activity table.
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
    return;
}

require_once __DIR__ . '/data.php';

WP_CLI::add_command( 'seed generate', function () {

    global $wpdb;

    WP_CLI::log( '--- Analytics Dashboard Seed ---' );

    // -------------------------------------------------------------------------
    // 1. Authors
    // -------------------------------------------------------------------------

    WP_CLI::log( 'Creating authors…' );

    $author_ids = [];

    foreach ( ad_seed_authors() as $author_data ) {
        $existing = get_user_by( 'email', $author_data['email'] );

        if ( $existing ) {
            $user_id = $existing->ID;
            WP_CLI::log( "  → Author already exists: {$author_data['display_name']} (ID {$user_id})" );
        } else {
            $user_id = wp_insert_user( [
                'user_login'   => $author_data['login'],
                'user_email'   => $author_data['email'],
                'display_name' => $author_data['display_name'],
                'first_name'   => $author_data['first_name'],
                'last_name'    => $author_data['last_name'],
                'role'         => 'author',
                'user_pass'    => wp_generate_password( 24, true, true ),
            ] );

            if ( is_wp_error( $user_id ) ) {
                WP_CLI::warning( "  Failed to create author {$author_data['display_name']}: " . $user_id->get_error_message() );
                continue;
            }

            WP_CLI::log( "  Created author: {$author_data['display_name']} (ID {$user_id})" );
        }

        update_user_meta( $user_id, 'dashboard_role', $author_data['dashboard_role'] );
        $author_ids[] = $user_id;
    }

    if ( empty( $author_ids ) ) {
        WP_CLI::error( 'No authors could be created or found. Aborting.' );
    }

    WP_CLI::log( count( $author_ids ) . ' author(s) ready.' );

    // -------------------------------------------------------------------------
    // 2. Categories
    // -------------------------------------------------------------------------

    WP_CLI::log( 'Creating categories…' );

    $category_map = []; // name => term_id

    foreach ( ad_seed_categories() as $cat_name ) {
        $slug     = sanitize_title( $cat_name );
        $existing = get_term_by( 'slug', $slug, 'category' );

        if ( $existing ) {
            $category_map[ $cat_name ] = (int) $existing->term_id;
            WP_CLI::log( "  → Category already exists: {$cat_name} (ID {$existing->term_id})" );
        } else {
            $result = wp_insert_term( $cat_name, 'category', [ 'slug' => $slug ] );

            if ( is_wp_error( $result ) ) {
                WP_CLI::warning( "  Failed to create category '{$cat_name}': " . $result->get_error_message() );
                continue;
            }

            $category_map[ $cat_name ] = (int) $result['term_id'];
            WP_CLI::log( "  Created category: {$cat_name} (ID {$result['term_id']})" );
        }
    }

    WP_CLI::log( count( $category_map ) . ' categor(y/ies) ready.' );

    // -------------------------------------------------------------------------
    // 3. Posts
    // -------------------------------------------------------------------------

    WP_CLI::log( 'Creating posts…' );

    $posts_data   = ad_seed_posts();
    $excerpts     = ad_seed_excerpts();
    $content      = ad_seed_content_block();

    $author_count  = count( $author_ids );
    $excerpt_count = count( $excerpts );

    // Spread published posts across the last 8 months.
    // We will assign dates from oldest to newest as we encounter published posts.
    $published_posts = array_filter( $posts_data, fn( $p ) => $p['status'] === 'publish' );
    $published_count = count( $published_posts ); // 30

    // Base: 8 months ago, stepping forward per published post.
    $eight_months_ago = strtotime( '-8 months' );
    $now              = time();
    $date_step        = ( $published_count > 1 )
        ? (int) ( ( $now - $eight_months_ago ) / ( $published_count - 1 ) )
        : 0;

    // Future posts: schedule 1–5 weeks ahead in order.
    $future_week = 1;

    $published_idx = 0; // counter for published date spreading
    $post_index    = 0; // global post counter (for author cycling, picsum seed, editor notes)

    $created_post_ids = []; // all IDs in insertion order

    // Track which post_id maps to which post data index (for activity events later).
    $first_post_id  = null; // will be used for activity events that reference specific posts
    $named_post_ids = []; // title slug → post_id

    foreach ( $posts_data as $post_data ) {
        $author_id  = $author_ids[ $post_index % $author_count ];
        $cat_name   = $post_data['category'];
        $excerpt    = $excerpts[ $post_index % $excerpt_count ];
        $status     = $post_data['status'];

        // Determine date.
        if ( $status === 'publish' ) {
            $post_ts   = $eight_months_ago + ( $published_idx * $date_step );
            $post_date = gmdate( 'Y-m-d H:i:s', $post_ts );
            $published_idx++;
        } elseif ( $status === 'future' ) {
            $post_date = gmdate( 'Y-m-d H:i:s', strtotime( "+{$future_week} week" ) );
            $future_week++;
        } else {
            // Draft: use current time.
            $post_date = current_time( 'mysql' );
        }

        $insert_args = [
            'post_title'   => $post_data['title'],
            'post_status'  => $status,
            'post_type'    => 'post',
            'post_author'  => $author_id,
            'post_content' => $content,
            'post_excerpt' => $excerpt,
            'post_date'    => $post_date,
            'post_date_gmt'=> ( $status === 'publish' )
                ? $post_date
                : get_gmt_from_date( $post_date ),
        ];

        // Assign category.
        if ( isset( $category_map[ $cat_name ] ) ) {
            $insert_args['post_category'] = [ $category_map[ $cat_name ] ];
        }

        $post_id = wp_insert_post( $insert_args, true );

        if ( is_wp_error( $post_id ) ) {
            WP_CLI::warning( "  Failed to create post \"{$post_data['title']}\": " . $post_id->get_error_message() );
            $post_index++;
            continue;
        }

        $created_post_ids[] = $post_id;

        // Remember first post id and keyed ids for activity events.
        if ( $first_post_id === null ) {
            $first_post_id = $post_id;
        }
        $named_post_ids[ sanitize_title( $post_data['title'] ) ] = $post_id;

        // ---- ACF fields ----
        // ~20 % of posts get empty SEO fields (posts 3, 7, 11, 15, 19, 23, 27, i.e. every 4th starting at index 3).
        $skip_seo = ( ( $post_index % 5 ) === 3 );

        if ( $status === 'publish' ) {
            if ( ! $skip_seo ) {
                update_field( 'seo_title',       $post_data['title'] . ' | Blog', $post_id );
                update_field( 'seo_description', $excerpt,                         $post_id );
            }
            update_field( 'reading_time_minutes', $post_data['reading_time'],  $post_id );
            update_field( 'views',                $post_data['views'],          $post_id );
            update_field( 'engagement_score',     $post_data['engagement'],     $post_id );
            update_field( 'avg_time_on_page_sec', $post_data['avg_time'],       $post_id );
            update_field( 'bounce_rate',          $post_data['bounce'],         $post_id );
            update_field( 'is_featured',          $post_data['is_featured'] ? 1 : 0, $post_id );
        } else {
            // Drafts and scheduled posts have reading_time set but no metrics.
            update_field( 'reading_time_minutes', $post_data['reading_time'], $post_id );
            update_field( 'is_featured', 0, $post_id );
        }

        // Editor note every 4th post (0-indexed: 0, 4, 8, …).
        if ( ( $post_index % 4 ) === 0 ) {
            update_field(
                'editor_note',
                "Priority piece — please review before the next publishing slot. Added by editor on " . gmdate( 'M j, Y' ) . '.',
                $post_id
            );
        }

        // Featured image URL as post meta (picsum.photos with post_id as seed).
        update_post_meta( $post_id, 'featured_image_url', "https://picsum.photos/seed/{$post_id}/800/400" );

        $num = $post_index + 1;
        WP_CLI::log( "  [{$num}/40] Created post ID {$post_id}: \"{$post_data['title']}\" [{$status}]" );

        $post_index++;
    }

    WP_CLI::log( count( $created_post_ids ) . ' post(s) created.' );

    // -------------------------------------------------------------------------
    // 4. Activity events
    // -------------------------------------------------------------------------

    WP_CLI::log( 'Inserting activity events…' );

    $activity_table = $wpdb->prefix . 'dashboard_activity';

    // Ensure the table exists (in case the plugin was not activated via the normal hook).
    $table_exists = $wpdb->get_var( "SHOW TABLES LIKE '{$activity_table}'" ) === $activity_table;

    if ( ! $table_exists ) {
        $charset_collate = $wpdb->get_charset_collate();
        $sql             = "CREATE TABLE IF NOT EXISTS {$activity_table} (
            id         BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            post_id    BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
            type       VARCHAR(50)         NOT NULL DEFAULT '',
            user       VARCHAR(100)        NOT NULL DEFAULT '',
            message    TEXT                NOT NULL,
            created_at DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY idx_post_id (post_id),
            KEY idx_type (type),
            KEY idx_created_at (created_at)
        ) {$charset_collate};";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta( $sql );
        WP_CLI::log( '  Activity table created.' );
    }

    $events         = ad_seed_activity_events();
    $event_count    = count( $events );
    $post_ids_count = count( $created_post_ids );

    // Space events across the last 60 days.
    $sixty_days_ago = strtotime( '-60 days' );
    $event_step     = (int) ( ( time() - $sixty_days_ago ) / max( $event_count, 1 ) );

    $inserted_events = 0;

    foreach ( $events as $i => $event ) {
        // Assign post_id: cycle through the created posts.
        $post_id    = ( $post_ids_count > 0 ) ? $created_post_ids[ $i % $post_ids_count ] : 0;
        $created_at = gmdate( 'Y-m-d H:i:s', $sixty_days_ago + ( $i * $event_step ) );

        $result = $wpdb->insert(
            $activity_table,
            [
                'post_id'    => $post_id,
                'type'       => $event['type'],
                'user'       => $event['user'],
                'message'    => $event['message'],
                'created_at' => $created_at,
            ],
            [ '%d', '%s', '%s', '%s', '%s' ]
        );

        if ( $result === false ) {
            WP_CLI::warning( "  Failed to insert activity event {$i}: " . $wpdb->last_error );
        } else {
            $inserted_events++;
        }
    }

    WP_CLI::log( "{$inserted_events} activity event(s) inserted." );

    // -------------------------------------------------------------------------
    // Done
    // -------------------------------------------------------------------------

    WP_CLI::success(
        sprintf(
            'Seed complete: %d author(s), %d categor(y/ies), %d post(s), %d activity event(s).',
            count( $author_ids ),
            count( $category_map ),
            count( $created_post_ids ),
            $inserted_events
        )
    );
} );
