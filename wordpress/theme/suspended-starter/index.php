<?php
/**
 * Suspended Starter – headless theme entry point.
 *
 * The frontend is served by Next.js. Any direct request to a WordPress URL
 * is redirected to the REST API index so clients know where to find content.
 */

// Redirect all frontend requests to the REST API root.
wp_redirect( rest_url(), 302 );
exit;
