<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_56a0f1f7826dd',
    'title' => 'Logotype',
    'fields' => array(
        0 => array(
            'return_format' => 'array',
            'preview_size' => 'thumbnail',
            'library' => 'uploadedTo',
            'min_width' => '',
            'min_height' => '',
            'min_size' => '',
            'max_width' => '',
            'max_height' => '',
            'max_size' => '',
            'mime_types' => 'svg',
            'key' => 'field_56a0f1fdbf847',
            'label' => __('Standard logotype', 'municipio'),
            'name' => 'logotype',
            'type' => 'image',
            'instructions' => __('Only .svg-files are supported.', 'municipio'),
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
        ),
        1 => array(
            'return_format' => 'array',
            'preview_size' => 'thumbnail',
            'library' => 'uploadedTo',
            'min_width' => '',
            'min_height' => '',
            'min_size' => '',
            'max_width' => '',
            'max_height' => '',
            'max_size' => '',
            'mime_types' => 'svg',
            'key' => 'field_56a0f5e3b4720',
            'label' => __('Negative logotype', 'municipio'),
            'name' => 'logotype_negative',
            'type' => 'image',
            'instructions' => __('Please upload a brand logo in the format .svg. The negative logo refers to a 100% white logotype that can be used on dark backgrounds.', 'municipio'),
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
        ),
    ),
    'location' => array(
        0 => array(
            0 => array(
                'param' => 'options_page',
                'operator' => '==',
                'value' => 'acf-options-theme-options',
            ),
        ),
    ),
    'menu_order' => -100,
    'position' => 'normal',
    'style' => 'default',
    'label_placement' => 'top',
    'instruction_placement' => 'label',
    'hide_on_screen' => '',
    'active' => 1,
    'description' => '',
    'local' => 'php',
));
}