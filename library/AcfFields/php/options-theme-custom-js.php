<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_573184999aa2c',
    'title' => 'Custom JS',
    'fields' => array(
        0 => array(
            'default_value' => '',
            'new_lines' => '',
            'maxlength' => '',
            'placeholder' => '',
            'rows' => 20,
            'key' => 'field_5731849ed0729',
            'label' => __('JavaScript', 'municipio'),
            'name' => 'custom_js_input',
            'type' => 'textarea',
            'instructions' => __('Enter your JS-code here. It will be wrapped with "script" tags and included on all pages.', 'municipio'),
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'readonly' => 0,
            'disabled' => 0,
        ),
    ),
    'location' => array(
        0 => array(
            0 => array(
                'param' => 'options_page',
                'operator' => '==',
                'value' => 'acf-options-css',
            ),
        ),
    ),
    'menu_order' => 0,
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