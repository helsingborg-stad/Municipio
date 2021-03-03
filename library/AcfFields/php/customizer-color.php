<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_60361b6d86d9d',
    'title' => __('Colors', 'municipio'),
    'fields' => array(
        0 => array(
            'key' => 'field_60361bcb76325',
            'label' => __('Primary Color', 'municipio'),
            'name' => 'municipio_primary_color',
            'type' => 'color_picker',
            'instructions' => '',
            'required' => 1,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'default_value' => __('#000', 'municipio'),
        ),
        1 => array(
            'key' => 'field_60364d06dc120',
            'label' => __('Secondary color', 'municipio'),
            'name' => 'municipio_secondary_color',
            'type' => 'color_picker',
            'instructions' => '',
            'required' => 1,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'default_value' => __('#eee', 'municipio'),
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