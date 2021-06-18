<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_60cb4dd20e9c3',
    'title' => __('Secondary navigation', 'municipio'),
    'fields' => array(
        0 => array(
            'key' => 'field_60cb4dd897cb8',
            'label' => __('Secondary navigation position', 'municipio'),
            'name' => 'secondary_navigation_position',
            'type' => 'radio',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'choices' => array(
                'left' => __('Left', 'municipio'),
                'right' => __('Right', 'municipio'),
                'hidden' => __('Hidden', 'municipio'),
            ),
            'allow_null' => 0,
            'other_choice' => 0,
            'default_value' => __('left', 'municipio'),
            'layout' => 'horizontal',
            'return_format' => 'value',
            'save_other_choice' => 0,
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