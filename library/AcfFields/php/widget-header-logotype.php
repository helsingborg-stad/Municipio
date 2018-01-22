<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_5a5ca31651f08',
    'title' => __('Widget header - Logotype', 'municipio'),
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
            'key' => 'field_5a65d7db67c96',
            'label' => '',
            'name' => '',
            'type' => 'clone',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'clone' => array(
                0 => 'group_5a65d5e7e913b',
            ),
            'display' => 'seamless',
            'layout' => 'block',
            'prefix_label' => 0,
            'prefix_name' => 0,
        ),
    ),
    'location' => array(
        0 => array(
            0 => array(
                'param' => 'widget',
                'operator' => '==',
                'value' => 'widget_header_header_widget_logo',
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