<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_614331a86b081',
    'title' => __('Padding', 'municipio'),
    'fields' => array(
        0 => array(
            'key' => 'field_6143323a35fa1',
            'label' => __('Content padding', 'municipio'),
            'name' => 'content_padding',
            'type' => 'group',
            'instructions' => __('Adjust the amount of padding around the columns area.', 'municipio'),
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'layout' => 'block',
            'sub_fields' => array(
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
        ),
    ),
    'location' => array(
        0 => array(
            0 => array(
                'param' => 'customizer',
                'operator' => '==',
                'value' => 'padding',
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