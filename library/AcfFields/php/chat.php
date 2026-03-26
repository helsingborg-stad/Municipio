<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_69b814ff11562',
    'title' => __('Chat Settings', 'municipio'),
    'fields' => array(
        0 => array(
            'key' => 'field_69b814ffe90d1',
            'label' => __('Chat URL', 'municipio'),
            'name' => 'chat_url',
            'aria-label' => '',
            'type' => 'url',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'default_value' => '',
            'allow_in_bindings' => 0,
            'placeholder' => '',
        ),
        1 => array(
            'key' => 'field_69b97761d5880',
            'label' => __('Chat API Key', 'municipio'),
            'name' => 'chat_api_key',
            'aria-label' => '',
            'type' => 'password',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'allow_in_bindings' => 0,
            'placeholder' => '',
            'prepend' => '',
            'append' => '',
        ),
        2 => array(
            'key' => 'field_69b97783d5881',
            'label' => __('Chat Assistant ID', 'municipio'),
            'name' => 'chat_assistant_id',
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
                'value' => 'chat-settings',
            ),
        ),
        1 => array(
            0 => array(
                'param' => 'block',
                'operator' => '==',
                'value' => 'acf/chat',
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
));
}