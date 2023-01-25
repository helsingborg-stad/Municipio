<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_63cfdba21f7fc',
    'title' => __('Container Block', 'municipio'),
    'fields' => array(
        0 => array(
            'key' => 'field_63cfdba39a6d2',
            'label' => __('Amount of padding', 'municipio'),
            'name' => 'amount',
            'type' => 'range',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'default_value' => 4,
            'min' => 0,
            'max' => 24,
            'step' => '',
            'prepend' => '',
            'append' => '',
        ),
        1 => array(
            'key' => 'field_63cfdc219a6d3',
            'label' => __('Background color', 'municipio'),
            'name' => 'color',
            'type' => 'color_picker',
            'instructions' => '',
            'required' => 1,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'default_value' => 'rgba(238, 238, 238, 1)',
            'enable_opacity' => 1,
            'return_format' => 'string',
        ),
    ),
    'location' => array(
        0 => array(
            0 => array(
                'param' => 'block',
                'operator' => '==',
                'value' => 'acf/container',
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
    'acfe_note' => '',
));
}