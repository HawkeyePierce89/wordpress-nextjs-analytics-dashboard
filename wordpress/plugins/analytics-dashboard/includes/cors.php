<?php
/**
 * CORS – allow the Next.js frontend running on localhost:3000 to access
 * the WordPress REST API during local development.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Send CORS headers for every REST API response.
 */
add_action( 'rest_api_init', function () {
    remove_filter( 'rest_pre_serve_request', 'rest_send_cors_headers' );

    add_filter( 'rest_pre_serve_request', function ( $value ) {
        $allowed_origins = [
            'http://localhost:3000',
            'http://127.0.0.1:3000',
        ];

        $origin = isset( $_SERVER['HTTP_ORIGIN'] ) ? $_SERVER['HTTP_ORIGIN'] : '';

        if ( in_array( $origin, $allowed_origins, true ) ) {
            header( 'Access-Control-Allow-Origin: ' . $origin );
        } else {
            // Fallback for requests without an Origin header (e.g. curl, Postman).
            header( 'Access-Control-Allow-Origin: http://localhost:3000' );
        }

        header( 'Access-Control-Allow-Credentials: true' );
        header( 'Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS' );
        header( 'Access-Control-Allow-Headers: Authorization, Content-Type, X-WP-Nonce' );
        header( 'Access-Control-Max-Age: 600' );

        return $value;
    } );
}, 15 );

/**
 * Handle OPTIONS preflight requests before WordPress routes them.
 * This must run early so WordPress does not attempt to authenticate the request.
 */
add_action( 'init', function () {
    if ( 'OPTIONS' === $_SERVER['REQUEST_METHOD'] ) {
        $allowed_origins = [
            'http://localhost:3000',
            'http://127.0.0.1:3000',
        ];

        $origin = isset( $_SERVER['HTTP_ORIGIN'] ) ? $_SERVER['HTTP_ORIGIN'] : '';

        if ( in_array( $origin, $allowed_origins, true ) ) {
            header( 'Access-Control-Allow-Origin: ' . $origin );
        } else {
            header( 'Access-Control-Allow-Origin: http://localhost:3000' );
        }

        header( 'Access-Control-Allow-Credentials: true' );
        header( 'Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS' );
        header( 'Access-Control-Allow-Headers: Authorization, Content-Type, X-WP-Nonce' );
        header( 'Access-Control-Max-Age: 600' );
        header( 'Content-Length: 0' );
        header( 'Content-Type: text/plain' );
        status_header( 204 );
        exit;
    }
}, 1 );
