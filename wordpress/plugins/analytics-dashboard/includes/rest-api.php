<?php
/**
 * REST API – Analytics Dashboard endpoints.
 *
 * Namespace : dashboard/v1
 * Endpoints :
 *   GET    /posts
 *   GET    /posts/<id>
 *   PATCH  /posts/<id>/metadata
 *   GET    /overview
 *   GET    /analytics
 *   GET    /authors
 *   GET    /categories
 */

defined( 'ABSPATH' ) || exit;

// ---------------------------------------------------------------------------
// Status mapping helpers
// ---------------------------------------------------------------------------

/**
 * Map API status string → WordPress post_status.
 *
 * @param string $status  API status value.
 * @return string         WordPress post_status.
 */
function ad_api_status_to_wp( $status ) {
    if ( 'scheduled' === $status ) {
        return 'future';
    }
    return $status; // 'publish', 'draft' pass through unchanged
}

/**
 * Map WordPress post_status → API status string.
 *
 * @param string $status  WordPress post_status.
 * @return string         API status value.
 */
function ad_wp_status_to_api( $status ) {
    if ( 'publish' === $status ) {
        return 'published';
    }
    if ( 'future' === $status ) {
        return 'scheduled';
    }
    return $status; // 'draft' passes through unchanged
}

// ---------------------------------------------------------------------------
// Post formatter
// ---------------------------------------------------------------------------

/**
 * Convert a WP_Post object into the API response shape.
 *
 * @param WP_Post $post
 * @return array
 */
function ad_format_post( $post ) {
    // Author data
    $author_id   = (int) $post->post_author;
    $author_user = get_userdata( $author_id );
    $author_name = $author_user ? $author_user->display_name : '';
    $avatar_url  = get_avatar_url( $author_id, [ 'size' => 96 ] );
    $role        = get_user_meta( $author_id, 'dashboard_role', true );
    if ( empty( $role ) ) {
        $role = 'Editor';
    }

    // Categories
    $terms      = get_the_terms( $post->ID, 'category' ) ?: [];
    $categories = [];
    foreach ( $terms as $term ) {
        $categories[] = [
            'id'   => (int) $term->term_id,
            'name' => $term->name,
            'slug' => $term->slug,
        ];
    }

    // Featured image
    $featured_image_url = null;
    $thumbnail_id       = get_post_thumbnail_id( $post->ID );
    if ( $thumbnail_id ) {
        $featured_image_url = wp_get_attachment_url( $thumbnail_id ) ?: null;
    }
    if ( ! $featured_image_url ) {
        $meta_img = get_post_meta( $post->ID, 'featured_image_url', true );
        if ( $meta_img ) {
            $featured_image_url = $meta_img;
        }
    }

    // Dates
    $api_status    = ad_wp_status_to_api( $post->post_status );
    $published_at  = null;
    if ( in_array( $api_status, [ 'published', 'scheduled' ], true ) ) {
        $published_at = gmdate( 'c', strtotime( $post->post_date_gmt . ' UTC' ) );
    }
    $updated_at = gmdate( 'c', strtotime( $post->post_modified_gmt . ' UTC' ) );

    // ACF fields
    $reading_time      = (int) get_field( 'reading_time_minutes', $post->ID );
    $seo_title         = (string) get_field( 'seo_title', $post->ID );
    $seo_description   = (string) get_field( 'seo_description', $post->ID );
    $views             = (int) get_field( 'views', $post->ID );
    $engagement_score  = (float) get_field( 'engagement_score', $post->ID );
    $avg_time_on_page  = (int) get_field( 'avg_time_on_page_sec', $post->ID );
    $bounce_rate       = (float) get_field( 'bounce_rate', $post->ID );
    $is_featured       = (bool) get_field( 'is_featured', $post->ID );
    $editor_note_raw   = get_field( 'editor_note', $post->ID );
    $editor_note       = ( $editor_note_raw !== false && $editor_note_raw !== '' ) ? $editor_note_raw : null;

    return [
        'id'                 => (int) $post->ID,
        'title'              => $post->post_title,
        'slug'               => $post->post_name,
        'excerpt'            => wp_strip_all_tags( $post->post_excerpt ),
        'content'            => apply_filters( 'the_content', $post->post_content ),
        'status'             => $api_status,
        'author'             => [
            'id'         => $author_id,
            'name'       => $author_name,
            'avatar_url' => $avatar_url,
            'role'       => $role,
        ],
        'categories'         => $categories,
        'featured_image_url' => $featured_image_url,
        'published_at'       => $published_at,
        'updated_at'         => $updated_at,
        'reading_time_minutes' => $reading_time,
        'seo'                => [
            'title'       => $seo_title,
            'description' => $seo_description,
        ],
        'metrics'            => [
            'views'               => $views,
            'engagement_score'    => $engagement_score,
            'avg_time_on_page_sec' => $avg_time_on_page,
            'bounce_rate'         => $bounce_rate,
        ],
        'is_featured'        => $is_featured,
        'editor_note'        => $editor_note,
    ];
}

// ---------------------------------------------------------------------------
// Register all endpoints in a single rest_api_init callback
// ---------------------------------------------------------------------------

add_action( 'rest_api_init', function () {

    $namespace = 'dashboard/v1';

    // -------------------------------------------------------------------------
    // GET /dashboard/v1/posts
    // -------------------------------------------------------------------------
    register_rest_route( $namespace, '/posts', [
        'methods'             => WP_REST_Server::READABLE,
        'callback'            => 'ad_get_posts',
        'permission_callback' => '__return_true',
        'args'                => [
            'search'      => [ 'type' => 'string',  'default' => '' ],
            'status'      => [ 'type' => 'string',  'default' => '' ],
            'author_id'   => [ 'type' => 'integer', 'default' => 0 ],
            'category_id' => [ 'type' => 'integer', 'default' => 0 ],
            'page'        => [ 'type' => 'integer', 'default' => 1,     'minimum' => 1 ],
            'per_page'    => [ 'type' => 'integer', 'default' => 10,    'minimum' => 1, 'maximum' => 100 ],
            'sort_by'     => [ 'type' => 'string',  'default' => 'date', 'enum' => [ 'date', 'title', 'views', 'engagement' ] ],
            'sort_order'  => [ 'type' => 'string',  'default' => 'desc', 'enum' => [ 'asc', 'desc' ] ],
        ],
    ] );

    // -------------------------------------------------------------------------
    // GET /dashboard/v1/posts/<id>
    // -------------------------------------------------------------------------
    register_rest_route( $namespace, '/posts/(?P<id>\d+)', [
        'methods'             => WP_REST_Server::READABLE,
        'callback'            => 'ad_get_post_detail',
        'permission_callback' => '__return_true',
        'args'                => [
            'id' => [ 'type' => 'integer', 'required' => true ],
        ],
    ] );

    // -------------------------------------------------------------------------
    // PATCH /dashboard/v1/posts/<id>/metadata
    // -------------------------------------------------------------------------
    register_rest_route( $namespace, '/posts/(?P<id>\d+)/metadata', [
        'methods'             => 'PATCH',
        'callback'            => 'ad_update_post_metadata',
        'permission_callback' => '__return_true',
        'args'                => [
            'id'                    => [ 'type' => 'integer', 'required' => true ],
            'seo_title'             => [ 'type' => 'string' ],
            'seo_description'       => [ 'type' => 'string' ],
            'reading_time_minutes'  => [ 'type' => 'integer', 'minimum' => 0 ],
            'views'                 => [ 'type' => 'integer', 'minimum' => 0 ],
            'engagement_score'      => [ 'type' => 'number',  'minimum' => 0, 'maximum' => 100 ],
            'is_featured'           => [ 'type' => 'boolean' ],
            'editor_note'           => [ 'type' => 'string' ],
        ],
    ] );

    // -------------------------------------------------------------------------
    // GET /dashboard/v1/overview
    // -------------------------------------------------------------------------
    register_rest_route( $namespace, '/overview', [
        'methods'             => WP_REST_Server::READABLE,
        'callback'            => 'ad_get_overview',
        'permission_callback' => '__return_true',
    ] );

    // -------------------------------------------------------------------------
    // GET /dashboard/v1/analytics
    // -------------------------------------------------------------------------
    register_rest_route( $namespace, '/analytics', [
        'methods'             => WP_REST_Server::READABLE,
        'callback'            => 'ad_get_analytics',
        'permission_callback' => '__return_true',
    ] );

    // -------------------------------------------------------------------------
    // GET /dashboard/v1/authors
    // -------------------------------------------------------------------------
    register_rest_route( $namespace, '/authors', [
        'methods'             => WP_REST_Server::READABLE,
        'callback'            => 'ad_get_authors',
        'permission_callback' => '__return_true',
    ] );

    // -------------------------------------------------------------------------
    // GET /dashboard/v1/categories
    // -------------------------------------------------------------------------
    register_rest_route( $namespace, '/categories', [
        'methods'             => WP_REST_Server::READABLE,
        'callback'            => 'ad_get_categories',
        'permission_callback' => '__return_true',
    ] );
} );

// ---------------------------------------------------------------------------
// Endpoint: GET /dashboard/v1/posts
// ---------------------------------------------------------------------------

/**
 * Return a paginated, filterable list of posts.
 *
 * @param WP_REST_Request $request
 * @return WP_REST_Response
 */
function ad_get_posts( $request ) {
    $page        = (int) $request->get_param( 'page' );
    $per_page    = (int) $request->get_param( 'per_page' );
    $search      = sanitize_text_field( $request->get_param( 'search' ) );
    $status      = sanitize_text_field( $request->get_param( 'status' ) );
    $author_id   = (int) $request->get_param( 'author_id' );
    $category_id = (int) $request->get_param( 'category_id' );
    $sort_by     = sanitize_text_field( $request->get_param( 'sort_by' ) );
    $sort_order  = strtoupper( sanitize_text_field( $request->get_param( 'sort_order' ) ) );

    // Base query args
    $args = [
        'post_type'      => 'post',
        'post_status'    => [ 'publish', 'draft', 'future' ],
        'posts_per_page' => $per_page,
        'paged'          => $page,
        'no_found_rows'  => false,
    ];

    // Search
    if ( $search !== '' ) {
        $args['s'] = $search;
    }

    // Status filter
    if ( $status !== '' ) {
        $wp_status = ad_api_status_to_wp( $status );
        if ( in_array( $wp_status, [ 'publish', 'draft', 'future' ], true ) ) {
            $args['post_status'] = $wp_status;
        }
    }

    // Author filter
    if ( $author_id > 0 ) {
        $args['author'] = $author_id;
    }

    // Category filter
    if ( $category_id > 0 ) {
        $args['cat'] = $category_id;
    }

    // Sorting
    switch ( $sort_by ) {
        case 'title':
            $args['orderby'] = 'title';
            $args['order']   = $sort_order;
            break;

        case 'views':
            $args['orderby']       = 'meta_value_num';
            $args['meta_key']      = 'views';
            $args['order']         = $sort_order;
            $args['meta_query']    = [
                'relation' => 'OR',
                [
                    'key'     => 'views',
                    'compare' => 'EXISTS',
                ],
                [
                    'key'     => 'views',
                    'compare' => 'NOT EXISTS',
                ],
            ];
            break;

        case 'engagement':
            $args['orderby']    = 'meta_value_num';
            $args['meta_key']   = 'engagement_score';
            $args['order']      = $sort_order;
            $args['meta_query'] = [
                'relation' => 'OR',
                [
                    'key'     => 'engagement_score',
                    'compare' => 'EXISTS',
                ],
                [
                    'key'     => 'engagement_score',
                    'compare' => 'NOT EXISTS',
                ],
            ];
            break;

        case 'date':
        default:
            $args['orderby'] = 'date';
            $args['order']   = $sort_order;
            break;
    }

    $query = new WP_Query( $args );

    $items = [];
    foreach ( $query->posts as $post ) {
        $items[] = ad_format_post( $post );
    }

    $total       = (int) $query->found_posts;
    $total_pages = (int) $query->max_num_pages;

    return rest_ensure_response( [
        'items'       => $items,
        'total'       => $total,
        'page'        => $page,
        'per_page'    => $per_page,
        'total_pages' => $total_pages,
    ] );
}

// ---------------------------------------------------------------------------
// Endpoint: GET /dashboard/v1/posts/<id>
// ---------------------------------------------------------------------------

/**
 * Return a single post with activity log and related posts.
 *
 * @param WP_REST_Request $request
 * @return WP_REST_Response|WP_Error
 */
function ad_get_post_detail( $request ) {
    global $wpdb;

    $post_id = (int) $request->get_param( 'id' );
    $post    = get_post( $post_id );

    if ( ! $post || $post->post_type !== 'post' ) {
        return new WP_Error(
            'rest_post_not_found',
            __( 'Post not found.', 'analytics-dashboard' ),
            [ 'status' => 404 ]
        );
    }

    // Activity events
    $activity_table = $wpdb->prefix . 'dashboard_activity';
    $activity       = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT * FROM {$activity_table} WHERE post_id = %d ORDER BY created_at DESC LIMIT 20",
            $post_id
        ),
        ARRAY_A
    );

    // Related posts: share at least one category, exclude current, up to 3
    $cat_ids = wp_get_post_categories( $post_id );
    $related = [];
    if ( ! empty( $cat_ids ) ) {
        $related_query = new WP_Query( [
            'post_type'      => 'post',
            'post_status'    => 'publish',
            'posts_per_page' => 3,
            'post__not_in'   => [ $post_id ],
            'category__in'   => $cat_ids,
            'orderby'        => 'date',
            'order'          => 'DESC',
            'no_found_rows'  => true,
        ] );
        foreach ( $related_query->posts as $related_post ) {
            $related[] = [
                'id'    => (int) $related_post->ID,
                'title' => $related_post->post_title,
                'slug'  => $related_post->post_name,
            ];
        }
    }

    return rest_ensure_response( [
        'post'          => ad_format_post( $post ),
        'activity'      => $activity ?: [],
        'related_posts' => $related,
    ] );
}

// ---------------------------------------------------------------------------
// Endpoint: PATCH /dashboard/v1/posts/<id>/metadata
// ---------------------------------------------------------------------------

/**
 * Update ACF metadata for a post.
 *
 * @param WP_REST_Request $request
 * @return WP_REST_Response|WP_Error
 */
function ad_update_post_metadata( $request ) {
    $post_id = (int) $request->get_param( 'id' );
    $post    = get_post( $post_id );

    if ( ! $post || $post->post_type !== 'post' ) {
        return new WP_Error(
            'rest_post_not_found',
            __( 'Post not found.', 'analytics-dashboard' ),
            [ 'status' => 404 ]
        );
    }

    $updatable_fields = [
        'seo_title',
        'seo_description',
        'reading_time_minutes',
        'views',
        'engagement_score',
        'is_featured',
        'editor_note',
    ];

    foreach ( $updatable_fields as $field ) {
        $value = $request->get_param( $field );
        if ( $value !== null ) {
            update_field( $field, $value, $post_id );
        }
    }

    // Re-fetch post to return fresh data
    $updated_post = get_post( $post_id );

    return rest_ensure_response( [
        'post' => ad_format_post( $updated_post ),
    ] );
}

// ---------------------------------------------------------------------------
// Endpoint: GET /dashboard/v1/overview
// ---------------------------------------------------------------------------

/**
 * Return aggregate overview statistics.
 *
 * @return WP_REST_Response
 */
function ad_get_overview() {
    global $wpdb;

    // Post counts
    $counts    = wp_count_posts( 'post' );
    $published = (int) $counts->publish;
    $draft     = (int) $counts->draft;
    $scheduled = (int) $counts->future;
    $total     = $published + $draft + $scheduled;

    // Total distinct authors
    $total_authors = (int) $wpdb->get_var(
        "SELECT COUNT(DISTINCT post_author)
         FROM {$wpdb->posts}
         WHERE post_type = 'post'
           AND post_status IN ('publish','draft','future')"
    );

    // Average reading time
    $avg_reading_time = (float) $wpdb->get_var(
        "SELECT AVG(CAST(meta_value AS DECIMAL(10,2)))
         FROM {$wpdb->postmeta} pm
         INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
         WHERE pm.meta_key = 'reading_time_minutes'
           AND p.post_type = 'post'
           AND p.post_status IN ('publish','draft','future')"
    );

    // Total views
    $total_views = (int) $wpdb->get_var(
        "SELECT SUM(CAST(meta_value AS UNSIGNED))
         FROM {$wpdb->postmeta} pm
         INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
         WHERE pm.meta_key = 'views'
           AND p.post_type = 'post'
           AND p.post_status IN ('publish','draft','future')"
    );

    // Average engagement score
    $avg_engagement_score = (float) $wpdb->get_var(
        "SELECT AVG(CAST(meta_value AS DECIMAL(10,2)))
         FROM {$wpdb->postmeta} pm
         INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
         WHERE pm.meta_key = 'engagement_score'
           AND p.post_type = 'post'
           AND p.post_status IN ('publish','draft','future')"
    );

    // Posts per month – last 12 months
    $posts_per_month_rows = $wpdb->get_results(
        "SELECT DATE_FORMAT(post_date, '%Y-%m') AS month,
                COUNT(*) AS count
         FROM {$wpdb->posts}
         WHERE post_type = 'post'
           AND post_status IN ('publish','draft','future')
           AND post_date >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
         GROUP BY month
         ORDER BY month ASC",
        ARRAY_A
    );
    $posts_per_month = [];
    foreach ( $posts_per_month_rows as $row ) {
        $posts_per_month[] = [
            'month' => $row['month'],
            'count' => (int) $row['count'],
        ];
    }

    // Posts by category
    $posts_by_category_rows = $wpdb->get_results(
        "SELECT t.name AS category, COUNT(DISTINCT tr.object_id) AS count
         FROM {$wpdb->term_relationships} tr
         INNER JOIN {$wpdb->term_taxonomy} tt ON tt.term_taxonomy_id = tr.term_taxonomy_id
         INNER JOIN {$wpdb->terms} t ON t.term_id = tt.term_id
         INNER JOIN {$wpdb->posts} p ON p.ID = tr.object_id
         WHERE tt.taxonomy = 'category'
           AND p.post_type = 'post'
           AND p.post_status IN ('publish','draft','future')
         GROUP BY t.name
         ORDER BY count DESC",
        ARRAY_A
    );
    $posts_by_category = [];
    foreach ( $posts_by_category_rows as $row ) {
        $posts_by_category[] = [
            'category' => $row['category'],
            'count'    => (int) $row['count'],
        ];
    }

    // Top 5 posts by views (published only)
    $top_posts_rows = $wpdb->get_results(
        "SELECT p.ID, p.post_title, p.post_name,
                CAST(pm.meta_value AS UNSIGNED) AS views
         FROM {$wpdb->posts} p
         INNER JOIN {$wpdb->postmeta} pm ON pm.post_id = p.ID AND pm.meta_key = 'views'
         WHERE p.post_type = 'post'
           AND p.post_status = 'publish'
         ORDER BY views DESC
         LIMIT 5",
        ARRAY_A
    );
    $top_posts = [];
    foreach ( $top_posts_rows as $row ) {
        $top_posts[] = [
            'id'    => (int) $row['ID'],
            'title' => $row['post_title'],
            'slug'  => $row['post_name'],
            'views' => (int) $row['views'],
        ];
    }

    // Recent activity – last 10 events
    $activity_table  = $wpdb->prefix . 'dashboard_activity';
    $recent_activity = $wpdb->get_results(
        "SELECT * FROM {$activity_table} ORDER BY created_at DESC LIMIT 10",
        ARRAY_A
    );

    return rest_ensure_response( [
        'counts'               => [
            'total'     => $total,
            'published' => $published,
            'draft'     => $draft,
            'scheduled' => $scheduled,
        ],
        'total_authors'        => $total_authors,
        'avg_reading_time'     => round( $avg_reading_time, 2 ),
        'total_views'          => $total_views,
        'avg_engagement_score' => round( $avg_engagement_score, 2 ),
        'posts_per_month'      => $posts_per_month,
        'posts_by_category'    => $posts_by_category,
        'top_posts'            => $top_posts,
        'recent_activity'      => $recent_activity ?: [],
    ] );
}

// ---------------------------------------------------------------------------
// Endpoint: GET /dashboard/v1/analytics
// ---------------------------------------------------------------------------

/**
 * Return detailed analytics data.
 *
 * @return WP_REST_Response
 */
function ad_get_analytics() {
    global $wpdb;

    // Posts per month – last 12 months
    $ppm_rows = $wpdb->get_results(
        "SELECT DATE_FORMAT(post_date, '%Y-%m') AS month,
                COUNT(*) AS count
         FROM {$wpdb->posts}
         WHERE post_type = 'post'
           AND post_status IN ('publish','draft','future')
           AND post_date >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
         GROUP BY month
         ORDER BY month ASC",
        ARRAY_A
    );
    $posts_per_month = [];
    foreach ( $ppm_rows as $row ) {
        $posts_per_month[] = [
            'month' => $row['month'],
            'count' => (int) $row['count'],
        ];
    }

    // Top categories by post count
    $top_categories_rows = $wpdb->get_results(
        "SELECT t.name AS category, t.term_id AS id,
                COUNT(DISTINCT tr.object_id) AS count
         FROM {$wpdb->term_relationships} tr
         INNER JOIN {$wpdb->term_taxonomy} tt ON tt.term_taxonomy_id = tr.term_taxonomy_id
         INNER JOIN {$wpdb->terms} t ON t.term_id = tt.term_id
         INNER JOIN {$wpdb->posts} p ON p.ID = tr.object_id
         WHERE tt.taxonomy = 'category'
           AND p.post_type = 'post'
           AND p.post_status IN ('publish','draft','future')
         GROUP BY t.term_id, t.name
         ORDER BY count DESC",
        ARRAY_A
    );
    $top_categories = [];
    foreach ( $top_categories_rows as $row ) {
        $top_categories[] = [
            'id'       => (int) $row['id'],
            'category' => $row['category'],
            'count'    => (int) $row['count'],
        ];
    }

    // Top authors by post count
    $top_authors_rows = $wpdb->get_results(
        "SELECT p.post_author AS author_id,
                COUNT(*) AS count
         FROM {$wpdb->posts} p
         WHERE p.post_type = 'post'
           AND p.post_status IN ('publish','draft','future')
         GROUP BY p.post_author
         ORDER BY count DESC",
        ARRAY_A
    );
    $top_authors = [];
    foreach ( $top_authors_rows as $row ) {
        $user = get_userdata( (int) $row['author_id'] );
        if ( $user ) {
            $top_authors[] = [
                'id'    => (int) $row['author_id'],
                'name'  => $user->display_name,
                'count' => (int) $row['count'],
            ];
        }
    }

    // Draft vs published counts
    $counts             = wp_count_posts( 'post' );
    $draft_vs_published = [
        'draft'     => (int) $counts->draft,
        'published' => (int) $counts->publish,
        'scheduled' => (int) $counts->future,
    ];

    // Average reading time by category
    $art_rows = $wpdb->get_results(
        "SELECT t.name AS category,
                AVG(CAST(pm.meta_value AS DECIMAL(10,2))) AS avg_reading_time
         FROM {$wpdb->postmeta} pm
         INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
         INNER JOIN {$wpdb->term_relationships} tr ON tr.object_id = p.ID
         INNER JOIN {$wpdb->term_taxonomy} tt ON tt.term_taxonomy_id = tr.term_taxonomy_id
         INNER JOIN {$wpdb->terms} t ON t.term_id = tt.term_id
         WHERE pm.meta_key = 'reading_time_minutes'
           AND p.post_type = 'post'
           AND p.post_status IN ('publish','draft','future')
           AND tt.taxonomy = 'category'
         GROUP BY t.name
         ORDER BY avg_reading_time DESC",
        ARRAY_A
    );
    $avg_reading_time_by_category = [];
    foreach ( $art_rows as $row ) {
        $avg_reading_time_by_category[] = [
            'category'         => $row['category'],
            'avg_reading_time' => round( (float) $row['avg_reading_time'], 2 ),
        ];
    }

    // Top 5 posts by views (published only)
    $top_views_rows = $wpdb->get_results(
        "SELECT p.ID, p.post_title, p.post_name,
                CAST(pm.meta_value AS UNSIGNED) AS views
         FROM {$wpdb->posts} p
         INNER JOIN {$wpdb->postmeta} pm ON pm.post_id = p.ID AND pm.meta_key = 'views'
         WHERE p.post_type = 'post'
           AND p.post_status = 'publish'
         ORDER BY views DESC
         LIMIT 5",
        ARRAY_A
    );
    $top_posts_by_views = [];
    foreach ( $top_views_rows as $row ) {
        $top_posts_by_views[] = [
            'id'    => (int) $row['ID'],
            'title' => $row['post_title'],
            'slug'  => $row['post_name'],
            'views' => (int) $row['views'],
        ];
    }

    // Content health metrics
    $total_posts = (int) $wpdb->get_var(
        "SELECT COUNT(*)
         FROM {$wpdb->posts}
         WHERE post_type = 'post'
           AND post_status IN ('publish','draft','future')"
    );

    $missing_seo_count = (int) $wpdb->get_var(
        "SELECT COUNT(DISTINCT p.ID)
         FROM {$wpdb->posts} p
         LEFT JOIN {$wpdb->postmeta} pm ON pm.post_id = p.ID AND pm.meta_key = 'seo_description'
         WHERE p.post_type = 'post'
           AND p.post_status IN ('publish','draft','future')
           AND (pm.meta_value IS NULL OR pm.meta_value = '')"
    );

    $missing_seo_pct = $total_posts > 0
        ? round( ( $missing_seo_count / $total_posts ) * 100, 2 )
        : 0.0;

    $avg_reading_time_overall = (float) $wpdb->get_var(
        "SELECT AVG(CAST(meta_value AS DECIMAL(10,2)))
         FROM {$wpdb->postmeta} pm
         INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
         WHERE pm.meta_key = 'reading_time_minutes'
           AND p.post_type = 'post'
           AND p.post_status IN ('publish','draft','future')"
    );

    $top_category_row = $wpdb->get_row(
        "SELECT t.name AS category, COUNT(DISTINCT tr.object_id) AS count
         FROM {$wpdb->term_relationships} tr
         INNER JOIN {$wpdb->term_taxonomy} tt ON tt.term_taxonomy_id = tr.term_taxonomy_id
         INNER JOIN {$wpdb->terms} t ON t.term_id = tt.term_id
         INNER JOIN {$wpdb->posts} p ON p.ID = tr.object_id
         WHERE tt.taxonomy = 'category'
           AND p.post_type = 'post'
           AND p.post_status IN ('publish','draft','future')
         GROUP BY t.name
         ORDER BY count DESC
         LIMIT 1",
        ARRAY_A
    );

    $top_author_row = $wpdb->get_row(
        "SELECT post_author, COUNT(*) AS count
         FROM {$wpdb->posts}
         WHERE post_type = 'post'
           AND post_status IN ('publish','draft','future')
         GROUP BY post_author
         ORDER BY count DESC
         LIMIT 1",
        ARRAY_A
    );
    $top_author_name = '';
    if ( $top_author_row ) {
        $top_author_user = get_userdata( (int) $top_author_row['post_author'] );
        if ( $top_author_user ) {
            $top_author_name = $top_author_user->display_name;
        }
    }

    return rest_ensure_response( [
        'posts_per_month'              => $posts_per_month,
        'top_categories'               => $top_categories,
        'top_authors'                  => $top_authors,
        'draft_vs_published'           => $draft_vs_published,
        'avg_reading_time_by_category' => $avg_reading_time_by_category,
        'top_posts_by_views'           => $top_posts_by_views,
        'content_health'               => [
            'missing_seo_description_pct' => $missing_seo_pct,
            'avg_reading_time'            => round( $avg_reading_time_overall, 2 ),
            'top_category'                => $top_category_row ? $top_category_row['category'] : null,
            'top_author'                  => $top_author_name ?: null,
        ],
    ] );
}

// ---------------------------------------------------------------------------
// Endpoint: GET /dashboard/v1/authors
// ---------------------------------------------------------------------------

/**
 * Return all distinct post authors.
 *
 * @return WP_REST_Response
 */
function ad_get_authors() {
    global $wpdb;

    $author_ids = $wpdb->get_col(
        "SELECT DISTINCT post_author
         FROM {$wpdb->posts}
         WHERE post_type = 'post'
           AND post_status IN ('publish','draft','future')
         ORDER BY post_author ASC"
    );

    $authors = [];
    foreach ( $author_ids as $author_id ) {
        $user = get_userdata( (int) $author_id );
        if ( ! $user ) {
            continue;
        }
        $role = get_user_meta( (int) $author_id, 'dashboard_role', true );
        if ( empty( $role ) ) {
            $role = 'Editor';
        }
        $authors[] = [
            'id'         => (int) $author_id,
            'name'       => $user->display_name,
            'avatar_url' => get_avatar_url( (int) $author_id, [ 'size' => 96 ] ),
            'role'       => $role,
        ];
    }

    return rest_ensure_response( $authors );
}

// ---------------------------------------------------------------------------
// Endpoint: GET /dashboard/v1/categories
// ---------------------------------------------------------------------------

/**
 * Return all categories.
 *
 * @return WP_REST_Response
 */
function ad_get_categories() {
    $terms = get_terms( [
        'taxonomy'   => 'category',
        'hide_empty' => false,
        'orderby'    => 'name',
        'order'      => 'ASC',
    ] );

    $categories = [];
    foreach ( $terms as $term ) {
        $categories[] = [
            'id'   => (int) $term->term_id,
            'name' => $term->name,
            'slug' => $term->slug,
        ];
    }

    return rest_ensure_response( $categories );
}
