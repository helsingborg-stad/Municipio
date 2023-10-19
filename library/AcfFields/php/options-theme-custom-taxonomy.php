<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_56c5e23aa271c',
    'title' => __('Manage taxonomies', 'municipio'),
    'fields' => array(
        0 => array(
            'key' => 'field_56c5e243a1c79',
            'label' => __('Available dynamic taxonomies', 'municipio'),
            'name' => 'avabile_dynamic_taxonomies',
            'aria-label' => '',
            'type' => 'repeater',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'min' => 0,
            'max' => 0,
            'layout' => 'block',
            'button_label' => __('Add taxonomy', 'municipio'),
            'collapsed' => 'field_56c5e5d43eb50',
            'rows_per_page' => 20,
            'sub_fields' => array(
                0 => array(
                    'key' => 'field_56c5e5d43eb50',
                    'label' => __('Label', 'municipio'),
                    'name' => 'label',
                    'aria-label' => '',
                    'type' => 'text',
                    'instructions' => __('<span style="color: #ff0000;">Once you have published a post type the name cannot be changed!</span>', 'municipio'),
                    'required' => 1,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => 50,
                        'class' => '',
                        'id' => '',
                    ),
                    'default_value' => '',
                    'maxlength' => '',
                    'placeholder' => '',
                    'prepend' => '',
                    'append' => '',
                    'readonly' => 0,
                    'disabled' => 0,
                    'parent_repeater' => 'field_56c5e243a1c79',
                ),
                1 => array(
                    'key' => 'field_56c5e580621be',
                    'label' => __('Permalänk', 'municipio'),
                    'name' => 'slug',
                    'aria-label' => '',
                    'type' => 'text',
                    'instructions' => __('Only applies if you enable the option to publish your taxonomy.', 'municipio'),
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '50',
                        'class' => '',
                        'id' => '',
                    ),
                    'default_value' => '',
                    'maxlength' => '',
                    'placeholder' => '',
                    'prepend' => '',
                    'append' => '',
                    'parent_repeater' => 'field_56c5e243a1c79',
                ),
                2 => array(
                    'key' => 'field_652e1f271c4e6',
                    'label' => __('API Source URL', 'municipio'),
                    'name' => 'api_source_url',
                    'aria-label' => '',
                    'type' => 'url',
                    'instructions' => __('API Source for the taxonomy. If this is supplied and responds with a WordPress REST API endpoint for a taxonomy collection; the terms for this taxonomy will be served from the supplied endpoint.', 'municipio'),
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'default_value' => '',
                    'placeholder' => '',
                    'parent_repeater' => 'field_56c5e243a1c79',
                ),
                3 => array(
                    'key' => 'field_56c5e44ea1c7d',
                    'label' => __('Type of taxonomy', 'municipio'),
                    'name' => 'hierarchical',
                    'aria-label' => '',
                    'type' => 'radio',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => 50,
                        'class' => '',
                        'id' => '',
                    ),
                    'layout' => 'horizontal',
                    'choices' => array(
                        0 => __('Tags', 'municipio'),
                        1 => __('Kategorier', 'municipio'),
                    ),
                    'default_value' => 1,
                    'other_choice' => 0,
                    'save_other_choice' => 0,
                    'allow_null' => 0,
                    'return_format' => 'value',
                    'parent_repeater' => 'field_56c5e243a1c79',
                ),
                4 => array(
                    'key' => 'field_56c5e67222b8b',
                    'label' => __('Connect to post type(s)', 'municipio'),
                    'name' => 'connected_post_types',
                    'aria-label' => '',
                    'type' => 'select',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => 50,
                        'class' => '',
                        'id' => '',
                    ),
                    'multiple' => 1,
                    'allow_null' => 0,
                    'choices' => array(
                        'post' => __('post', 'municipio'),
                        'page' => __('page', 'municipio'),
                        'attachment' => __('attachment', 'municipio'),
                        'custom_css' => __('custom_css', 'municipio'),
                        'customize_changeset' => __('customize_changeset', 'municipio'),
                        'customer-feedback' => __('customer-feedback', 'municipio'),
                        'np-redirect' => __('np-redirect', 'municipio'),
                        'mod-text' => __('mod-text', 'municipio'),
                        'mod-contacts' => __('mod-contacts', 'municipio'),
                        'mod-fileslist' => __('mod-fileslist', 'municipio'),
                        'mod-gallery' => __('mod-gallery', 'municipio'),
                        'mod-iframe' => __('mod-iframe', 'municipio'),
                        'mod-image' => __('mod-image', 'municipio'),
                        'mod-index' => __('mod-index', 'municipio'),
                        'mod-inheritpost' => __('mod-inheritpost', 'municipio'),
                        'mod-inlaylist' => __('mod-inlaylist', 'municipio'),
                        'mod-notice' => __('mod-notice', 'municipio'),
                        'mod-posts' => __('mod-posts', 'municipio'),
                        'mod-script' => __('mod-script', 'municipio'),
                        'mod-slider' => __('mod-slider', 'municipio'),
                        'mod-social' => __('mod-social', 'municipio'),
                        'mod-table' => __('mod-table', 'municipio'),
                        'mod-video' => __('mod-video', 'municipio'),
                        'mod-wpwidget' => __('mod-wpwidget', 'municipio'),
                        'listing' => __('listing', 'municipio'),
                        'oembed_cache' => __('oembed_cache', 'municipio'),
                        'user_request' => __('user_request', 'municipio'),
                        'wp_block' => __('wp_block', 'municipio'),
                        'wp_template' => __('wp_template', 'municipio'),
                        'wp_template_part' => __('wp_template_part', 'municipio'),
                        'wp_global_styles' => __('wp_global_styles', 'municipio'),
                        'wp_navigation' => __('wp_navigation', 'municipio'),
                        'acf-taxonomy' => __('acf-taxonomy', 'municipio'),
                        'acf-post-type' => __('acf-post-type', 'municipio'),
                        'acf-ui-options-page' => __('acf-ui-options-page', 'municipio'),
                        'custom-short-link' => __('custom-short-link', 'municipio'),
                        'mod-breadcrumbs' => __('mod-breadcrumbs', 'municipio'),
                        'mod-curator' => __('mod-curator', 'municipio'),
                        'mod-divider' => __('mod-divider', 'municipio'),
                        'mod-hero' => __('mod-hero', 'municipio'),
                        'mod-logogrid' => __('mod-logogrid', 'municipio'),
                        'mod-map' => __('mod-map', 'municipio'),
                        'mod-modal' => __('mod-modal', 'municipio'),
                        'mod-rss' => __('mod-rss', 'municipio'),
                        'mod-sites' => __('mod-sites', 'municipio'),
                        'mod-spacer' => __('mod-spacer', 'municipio'),
                        'mod-subscribe' => __('mod-subscribe', 'municipio'),
                        'mod-contact-banner' => __('mod-contact-banner', 'municipio'),
                        'mod-form' => __('mod-form', 'municipio'),
                        'mod-open-street-map' => __('mod-open-street-map', 'municipio'),
                        'mod-products' => __('mod-products', 'municipio'),
                        'mod-recommend' => __('mod-recommend', 'municipio'),
                        'mod-section-split' => __('mod-section-split', 'municipio'),
                        'mod-section-full' => __('mod-section-full', 'municipio'),
                        'mod-section-featured' => __('mod-section-featured', 'municipio'),
                        'mod-section-card' => __('mod-section-card', 'municipio'),
                        'mod-testimonial-card' => __('mod-testimonial-card', 'municipio'),
                        'mod-timeline' => __('mod-timeline', 'municipio'),
                        'news' => __('news', 'municipio'),
                        'pre-school' => __('pre-school', 'municipio'),
                        'person' => __('person', 'municipio'),
                        'elementary-school' => __('elementary-school', 'municipio'),
                        'school-media' => __('school-media', 'municipio'),
                        'acfe-dbt' => __('acfe-dbt', 'municipio'),
                        'acfe-form' => __('acfe-form', 'municipio'),
                        'acfe-dop' => __('acfe-dop', 'municipio'),
                        'acfe-dpt' => __('acfe-dpt', 'municipio'),
                        'acfe-dt' => __('acfe-dt', 'municipio'),
                        'form-submissions' => __('form-submissions', 'municipio'),
                        'modal-content' => __('modal-content', 'municipio'),
                    ),
                    'default_value' => array(
                    ),
                    'ui' => 1,
                    'ajax' => 0,
                    'placeholder' => '',
                    'return_format' => 'value',
                    'disabled' => 0,
                    'readonly' => 0,
                    'parent_repeater' => 'field_56c5e243a1c79',
                ),
                5 => array(
                    'key' => 'field_56c5e2c4a1c7a',
                    'label' => __('Offentlig', 'municipio'),
                    'name' => 'public',
                    'aria-label' => '',
                    'type' => 'true_false',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => 50,
                        'class' => '',
                        'id' => '',
                    ),
                    'default_value' => 1,
                    'message' => __('Enable visitors to browse this taxonomy', 'municipio'),
                    'ui' => 0,
                    'ui_on_text' => '',
                    'ui_off_text' => '',
                    'parent_repeater' => 'field_56c5e243a1c79',
                ),
                6 => array(
                    'key' => 'field_56c5e2d3a1c7b',
                    'label' => __('Admin user interface', 'municipio'),
                    'name' => 'show_ui',
                    'aria-label' => '',
                    'type' => 'true_false',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => 50,
                        'class' => '',
                        'id' => '',
                    ),
                    'default_value' => 1,
                    'message' => __('Enable administration UI', 'municipio'),
                    'ui' => 0,
                    'ui_on_text' => '',
                    'ui_off_text' => '',
                    'parent_repeater' => 'field_56c5e243a1c79',
                ),
            ),
        ),
    ),
    'location' => array(
        0 => array(
            0 => array(
                'param' => 'options_page',
                'operator' => '==',
                'value' => 'acf-options-taxonomies',
            ),
        ),
    ),
    'menu_order' => 0,
    'position' => 'normal',
    'style' => 'default',
    'label_placement' => 'top',
    'instruction_placement' => 'field',
    'hide_on_screen' => '',
    'active' => true,
    'description' => '',
    'show_in_rest' => 0,
    'acfe_display_title' => '',
    'acfe_autosync' => '',
    'acfe_form' => 0,
    'acfe_meta' => '',
    'acfe_note' => '',
));
}