<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_6a21211157be7',
    'title' => __('Vitec settings', 'municipio'),
    'fields' => array(
        0 => array(
            'key' => 'field_6a2121116c5ae',
            'label' => __('API URL', 'municipio'),
            'name' => 'vitec_api_baseurl',
            'aria-label' => '',
            'type' => 'text',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'default_value' => '',
            'maxlength' => '',
            'allow_in_bindings' => 0,
            'placeholder' => __('https://website.com', 'municipio'),
            'prepend' => '',
            'append' => '',
        ),
        1 => array(
            'key' => 'field_6a2121ad86dc1',
            'label' => __('API Key', 'municipio'),
            'name' => 'vitec_api_key',
            'aria-label' => '',
            'type' => 'text',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'default_value' => '',
            'maxlength' => '',
            'allow_in_bindings' => 0,
            'placeholder' => '',
            'prepend' => '',
            'append' => '',
        ),
    ),
    'location' => array(
        0 => array(
            0 => array(
                'param' => 'options_page',
                'operator' => '==',
                'value' => 'vitec-settings',
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
    'show_in_rest' => 0,
    'display_title' => '',
    'allow_ai_access' => false,
    'ai_description' => '',
));
}