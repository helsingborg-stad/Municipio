<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_60361b6d86d9d',
    'title' => __('Color Profile', 'municipio'),
    'fields' => array(
        0 => array(
            'key' => 'field_60361bcb76325',
            'label' => __('Primary', 'municipio'),
            'name' => 'municipio_color_primary',
            'type' => 'color_picker',
            'instructions' => '',
            'required' => 1,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'default_value' => __('#ae0b05', 'municipio'),
        ),
        1 => array(
            'key' => 'field_603e01fa908e5',
            'label' => __('Primary dark', 'municipio'),
            'name' => 'municipio_color_primary_dark',
            'type' => 'color_picker',
            'instructions' => '',
            'required' => 1,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'default_value' => __('#770000', 'municipio'),
        ),
        2 => array(
            'key' => 'field_603e0b0e43957',
            'label' => __('Primary Light', 'municipio'),
            'name' => 'municipio_color_primary_light',
            'type' => 'color_picker',
            'instructions' => '',
            'required' => 1,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'default_value' => __('#e84c31', 'municipio'),
        ),
    ),
    'location' => array(
        0 => array(
            0 => array(
                'param' => 'customizer',
                'operator' => '==',
                'value' => 'colors',
            ),
        ),
    ),
    'menu_order' => 0,
    'position' => 'normal',
    'style' => 'default',
    'label_placement' => 'top',
    'instruction_placement' => 'label',
    'hide_on_screen' => '',
    'active' => true,
    'description' => '',
));
}