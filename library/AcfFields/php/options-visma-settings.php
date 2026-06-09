<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_6a215f59d5550',
    'title' => __('Visma settings', 'municipio'),
    'fields' => array(
        0 => array(
            'key' => 'field_6a215fb780686',
            'label' => __('Customer Key', 'municipio'),
            'name' => 'visma_auth_customerkey',
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
        1 => array(
            'key' => 'field_6a215f5a80685',
            'label' => __('Service Key', 'municipio'),
            'name' => 'visma_auth_servicekey',
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
        2 => array(
            'key' => 'field_6a215fd080687',
            'label' => __('Url', 'municipio'),
            'name' => 'visma_auth_baseurl',
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
                'value' => 'visma-settings',
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