<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_611e43c157cbb',
    'title' => __('Site columns padding', 'municipio'),
    'fields' => array(
        0 => array(
            'key' => 'field_611e43ec4dfa5',
            'label' => __('Amount of padding', 'municipio'),
            'name' => 'amount_of_padding_columns',
            'type' => 'range',
            'instructions' => __('Padding will be applied in 8px increments.', 'municipio'),
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'default_value' => 0,
            'min' => '',
            'max' => 12,
            'step' => 2,
            'prepend' => '',
            'append' => '',
        ),
    ),
    'location' => array(
        0 => array(
            0 => array(
                'param' => 'customizer',
                'operator' => '==',
                'value' => 'site',
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