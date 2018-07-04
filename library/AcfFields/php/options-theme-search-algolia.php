<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_5a61b852f3f8c',
    'title' => __('Algolia Search', 'municipio'),
    'fields' => array(
        0 => array(
            'key' => 'field_5a61b85c6e7b8',
            'label' => __('Enable', 'municipio'),
            'name' => 'use_algolia_search',
            'type' => 'true_false',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'message' => __('Enable the Algolia Search (Requires Algolia WordPress plugin)', 'municipio'),
            'default_value' => 0,
            'ui' => 0,
            'ui_on_text' => '',
            'ui_off_text' => '',
        ),
        1 => array(
            'key' => 'field_5b3c6dc1c3210',
            'label' => __('Display label for post types', 'municipio'),
            'name' => 'algolia_display_post_types',
            'type' => 'posttype_select',
            'instructions' => __('Displays a label for selected post types with their names in the search results.', 'municipio'),
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'default_value' => '',
            'allow_null' => 1,
            'multiple' => 1,
            'placeholder' => '',
            'disabled' => 0,
            'readonly' => 0,
        ),
    ),
    'location' => array(
        0 => array(
            0 => array(
                'param' => 'options_page',
                'operator' => '==',
                'value' => 'acf-options-search',
            ),
        ),
    ),
    'menu_order' => 0,
    'position' => 'normal',
    'style' => 'default',
    'label_placement' => 'top',
    'instruction_placement' => 'label',
    'hide_on_screen' => '',
    'active' => 1,
    'description' => '',
));
}