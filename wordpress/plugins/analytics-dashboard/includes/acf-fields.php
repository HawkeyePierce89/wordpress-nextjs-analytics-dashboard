<?php
/**
 * ACF Field Group – Post metadata for the analytics dashboard.
 *
 * Registers SEO, readability, and engagement metrics as custom fields
 * on all Posts. Fields are available via the ACF REST API integration.
 */

defined( 'ABSPATH' ) || exit;

add_action( 'acf/init', function () {
    if ( ! function_exists( 'acf_add_local_field_group' ) ) {
        return;
    }

    acf_add_local_field_group( [
        'key'                   => 'group_analytics_dashboard_post_meta',
        'title'                 => 'Analytics Dashboard – Post Metadata',
        'fields'                => [
            // -----------------------------------------------------------------
            // SEO
            // -----------------------------------------------------------------
            [
                'key'               => 'field_seo_title',
                'label'             => 'SEO Title',
                'name'              => 'seo_title',
                'type'              => 'text',
                'instructions'      => 'Recommended maximum: 60 characters.',
                'required'          => 0,
                'maxlength'         => 60,
                'placeholder'       => '',
                'prepend'           => '',
                'append'            => '',
            ],
            [
                'key'               => 'field_seo_description',
                'label'             => 'SEO Description',
                'name'              => 'seo_description',
                'type'              => 'textarea',
                'instructions'      => 'Recommended maximum: 160 characters.',
                'required'          => 0,
                'maxlength'         => 160,
                'rows'              => 3,
                'new_lines'         => '',
            ],
            // -----------------------------------------------------------------
            // Readability
            // -----------------------------------------------------------------
            [
                'key'               => 'field_reading_time_minutes',
                'label'             => 'Reading Time (minutes)',
                'name'              => 'reading_time_minutes',
                'type'              => 'number',
                'instructions'      => 'Estimated reading time in minutes.',
                'required'          => 0,
                'min'               => 0,
                'max'               => '',
                'step'              => 1,
                'prepend'           => '',
                'append'            => 'min',
            ],
            // -----------------------------------------------------------------
            // Engagement metrics
            // -----------------------------------------------------------------
            [
                'key'               => 'field_views',
                'label'             => 'Views',
                'name'              => 'views',
                'type'              => 'number',
                'instructions'      => 'Total page view count.',
                'required'          => 0,
                'min'               => 0,
                'max'               => '',
                'step'              => 1,
                'prepend'           => '',
                'append'            => '',
            ],
            [
                'key'               => 'field_engagement_score',
                'label'             => 'Engagement Score',
                'name'              => 'engagement_score',
                'type'              => 'number',
                'instructions'      => 'Composite engagement score from 0 to 100.',
                'required'          => 0,
                'min'               => 0,
                'max'               => 100,
                'step'              => 1,
                'prepend'           => '',
                'append'            => '',
            ],
            [
                'key'               => 'field_avg_time_on_page_sec',
                'label'             => 'Average Time on Page (seconds)',
                'name'              => 'avg_time_on_page_sec',
                'type'              => 'number',
                'instructions'      => 'Average time visitors spend on this post, in seconds.',
                'required'          => 0,
                'min'               => 0,
                'max'               => '',
                'step'              => 1,
                'prepend'           => '',
                'append'            => 'sec',
            ],
            [
                'key'               => 'field_bounce_rate',
                'label'             => 'Bounce Rate (%)',
                'name'              => 'bounce_rate',
                'type'              => 'number',
                'instructions'      => 'Bounce rate percentage from 0 to 100.',
                'required'          => 0,
                'min'               => 0,
                'max'               => 100,
                'step'              => 0.01,
                'prepend'           => '',
                'append'            => '%',
            ],
            // -----------------------------------------------------------------
            // Editorial
            // -----------------------------------------------------------------
            [
                'key'               => 'field_is_featured',
                'label'             => 'Featured Post',
                'name'              => 'is_featured',
                'type'              => 'true_false',
                'instructions'      => 'Mark this post as featured on the dashboard.',
                'required'          => 0,
                'message'           => 'Yes, feature this post',
                'default_value'     => 0,
                'ui'                => 1,
            ],
            [
                'key'               => 'field_editor_note',
                'label'             => 'Editor Note',
                'name'              => 'editor_note',
                'type'              => 'textarea',
                'instructions'      => 'Internal note visible only in the WordPress admin.',
                'required'          => 0,
                'maxlength'         => '',
                'rows'              => 4,
                'new_lines'         => 'br',
            ],
        ],
        'location'              => [
            [
                [
                    'param'    => 'post_type',
                    'operator' => '==',
                    'value'    => 'post',
                ],
            ],
        ],
        'menu_order'            => 0,
        'position'              => 'normal',
        'style'                 => 'default',
        'label_placement'       => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen'        => '',
        'active'                => true,
        'description'           => 'SEO metadata, readability metrics, and engagement analytics for individual posts.',
    ] );
} );
