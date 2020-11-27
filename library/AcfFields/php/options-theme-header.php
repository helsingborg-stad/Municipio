<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_56a22a9c78e54',
    'title' => 'Header',
    'fields' => array(
        0 => array(
            'key' => 'field_56a22aaa83835',
            'label' => __('Layout', 'municipio'),
            'name' => 'header_layout',
            'type' => 'radio',
            'instructions' => '',
            'required' => 1,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'choices' => array(
                'business' => __('Business (standardval)', 'municipio'),
                'casual' => __('Casual', 'municipio'),
            ),
            'allow_null' => 0,
            'other_choice' => 0,
            'default_value' => 'business',
            'layout' => 'vertical',
            'return_format' => 'value',
            'save_other_choice' => 0,
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
    'menu_order' => 2,
    'position' => 'normal',
    'style' => 'default',
    'label_placement' => 'top',
    'instruction_placement' => 'label',
    'hide_on_screen' => '',
    'active' => true,
    'description' => '',
));
}