<?php
/**
 * Plugin Name: Analytics Dashboard
 * Plugin URI:  https://github.com/antonkarmanov/wordpress-nextjs-analytics-dashboard
 * Description: Headless analytics plugin – exposes custom REST API endpoints, ACF field definitions, and activity logging for the Next.js analytics dashboard.
 * Version:     1.0.0
 * Author:      Anton Karmanov
 * Author URI:  https://github.com/antonkarmanov
 * License:     GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: analytics-dashboard
 * Requires at least: 6.0
 * Requires PHP: 8.1
 */

defined( 'ABSPATH' ) || exit;

// ---------------------------------------------------------------------------
// Activation hook – create the activity table.
// ---------------------------------------------------------------------------

register_activation_hook( __FILE__, function () {
    global $wpdb;

    $table_name      = $wpdb->prefix . 'dashboard_activity';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS {$table_name} (
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
} );

// ---------------------------------------------------------------------------
// Load required includes.
// ---------------------------------------------------------------------------

require_once __DIR__ . '/includes/cors.php';
require_once __DIR__ . '/includes/acf-fields.php';
require_once __DIR__ . '/includes/rest-api.php';

// ---------------------------------------------------------------------------
// Conditionally load the WP-CLI seed command.
// ---------------------------------------------------------------------------

if ( defined( 'WP_CLI' ) && WP_CLI ) {
    require_once __DIR__ . '/seed/seed.php';
}
