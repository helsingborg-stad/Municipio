<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_56c5d34a261f5',
    'title' => 'Header logotype',
    'fields' => array(
        0 => array(
            'layout' => 'vertical',
            'choices' => array(
                'standard' => __('Standard', 'municipio'),
                'negative' => __('Negative', 'municipio'),
            ),
            'default_value' => '',
            'other_choice' => 0,
            'save_other_choice' => 0,
            'allow_null' => 0,
            'return_format' => 'value',
            'key' => 'field_56c5d3530369f',
            'label' => __('Logotype to use in header', 'municipio'),
            'name' => 'header_logotype',
            'type' => 'radio',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
        ),
        1 => array(
            'default_value' => '',
            'maxlength' => '',
            'placeholder' => '',
            'prepend' => '',
            'append' => '',
            'key' => 'field_56c5d388036a0',
            'label' => __('Logotype tooltip', 'municipio'),
            'name' => 'logotype_tooltip',
            'type' => 'text',
            'instructions' => __('Tooltip text when hovering over the logotype in the header. The logo links to the front page. Leave empty if you don\'t want to show anything.', 'municipio'),
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
                'value' => 'acf-options-header',
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