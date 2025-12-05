<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_61bc6134601a0',
    'title' => __('Margin size', 'municipio'),
    'fields' => array(
        0 => array(
            'key' => 'field_61bc61423f527',
            'label' => __('Amount of margin', 'municipio'),
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
            'default_value' => 2,
            'min' => 1,
            'max' => 8,
            'step' => '',
            'prepend' => '',
            'append' => '',
        ),
    ),
    'location' => array(
        0 => array(
            0 => array(
                'param' => 'block',
                'operator' => '==',
                'value' => 'acf/margin',
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