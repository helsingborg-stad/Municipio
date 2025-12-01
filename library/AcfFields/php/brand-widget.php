<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_5a5ca31651f08',
    'title' => __('Brand / Logo Widget', 'municipio'),
    'fields' => array(
        0 => array(
            'key' => 'field_5a5ca31c83a1c',
            'label' => __('Upload logotype', 'municipio'),
            'name' => 'widget_header_logotype',
            'type' => 'image',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'return_format' => 'array',
            'preview_size' => 'medium',
            'library' => 'all',
            'min_width' => '',
            'min_height' => '',
            'min_size' => '',
            'max_width' => '',
            'max_height' => '',
            'max_size' => '',
            'mime_types' => 'svg,png',
        ),
        1 => array(
            'key' => 'field_5a7c6d854ae89',
            'label' => __('Max width', 'municipio'),
            'name' => 'widget_header_max_width',
            'type' => 'range',
            'instructions' => __('Choose the max width of the logo.', 'municipio'),
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'default_value' => 240,
            'min' => 48,
            'max' => 1480,
            'step' => 8,
            'prepend' => '',
            'append' => __('px', 'municipio'),
        ),
    ),
    'location' => array(
        0 => array(
            0 => array(
                'param' => 'widget',
                'operator' => '==',
                'value' => 'widget-header-logo',
            ),
        ),
        1 => array(
            0 => array(
                'param' => 'widget',
                'operator' => '==',
                'value' => 'brand-municipio',
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