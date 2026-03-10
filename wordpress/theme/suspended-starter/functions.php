<?php
/**
 * Suspended Starter – theme functions.
 *
 * Disables all unnecessary frontend features so WordPress operates
 * purely as a headless REST API backend.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Remove frontend-only features that are irrelevant in headless mode.
 */
add_action( 'init', function () {
    // Disable the WordPress frontend toolbar for logged-in users.
    add_filter( 'show_admin_bar', '__return_false' );

    // Remove default post formats support.
    remove_theme_support( 'post-formats' );
} );

/**
 * Disable emojis – reduces HTTP requests and injected scripts.
 */
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'admin_print_styles', 'print_emoji_styles' );

/**
 * Remove unnecessary <link> tags injected into <head>.
 */
remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'wp_shortlink_wp_head' );

/**
 * Disable XML-RPC – not needed for headless; reduces attack surface.
 */
add_filter( 'xmlrpc_enabled', '__return_false' );

/**
 * Expose the REST API to unauthenticated requests for public endpoints.
 * Authentication is handled per-endpoint where required.
 */
add_filter( 'rest_authentication_errors', function ( $result ) {
    if ( ! empty( $result ) ) {
        return $result;
    }
    return true;
} );
