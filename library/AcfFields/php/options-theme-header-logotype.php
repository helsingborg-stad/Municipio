<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_56c5d34a261f5',
    'title' => __('Header logotype', 'municipio'),
    'fields' => array(
        0 => array(
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
            'choices' => array(
                'standard' => __('Primary', 'municipio'),
                'negative' => __('Secondary', 'municipio'),
            ),
            'allow_null' => 0,
            'other_choice' => 0,
            'default_value' => '',
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
    'active' => true,
    'description' => '',
));
}