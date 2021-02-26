<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_603662f315acc',
    'title' => __('Radiuses', 'municipio'),
    'fields' => array(
        0 => array(
            'key' => 'field_603662f7a16f8',
            'label' => __('X-Small', 'municipio'),
            'name' => 'municipio_radius_xs',
            'type' => 'range',
            'instructions' => '',
            'required' => 1,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'default_value' => 2,
            'min' => 0,
            'max' => 16,
            'step' => 2,
            'prepend' => '',
            'append' => __('px', 'municipio'),
        ),
    ),
    'location' => array(
        0 => array(
            0 => array(
                'param' => 'customizer',
                'operator' => '==',
                'value' => 'radiuses',
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