<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_63e6002cc129c',
    'title' => __('Advanced term settings', 'municipio'),
    'fields' => array(
        0 => array(
            'key' => 'field_63e603e03afc7',
            'label' => __('Icon', 'municipio'),
            'name' => 'icon',
            'aria-label' => '',
            'type' => 'image',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '33',
                'class' => '',
                'id' => '',
            ),
            'uploader' => '',
            'acfe_thumbnail' => 0,
            'return_format' => 'id',
            'min_width' => '',
            'min_height' => '',
            'min_size' => '',
            'max_width' => '',
            'max_height' => '',
            'max_size' => '',
            'mime_types' => 'svg',
            'preview_size' => 'thumbnail',
            'library' => 'all',
        ),
        1 => array(
            'key' => 'field_63e6002d8aa4f',
            'label' => __('Colour', 'municipio'),
            'name' => 'colour',
            'aria-label' => '',
            'type' => 'color_picker',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '66',
                'class' => '',
                'id' => '',
            ),
            'default_value' => '',
            'enable_opacity' => 0,
            'return_format' => 'string',
        ),
    ),
    'location' => array(
        0 => array(
            0 => array(
                'param' => 'taxonomy',
                'operator' => '==',
                'value' => 'all',
            ),
        ),
    ),
    'menu_order' => 0,
    'position' => 'normal',
    'style' => 'default',
    'label_placement' => 'left',
    'instruction_placement' => 'label',
    'hide_on_screen' => '',
    'active' => true,
    'description' => '',
    'show_in_rest' => 0,
    'acfe_display_title' => '',
    'acfe_autosync' => '',
    'acfe_form' => 0,
    'acfe_meta' => '',
    'acfe_note' => 'För termer',
));
}