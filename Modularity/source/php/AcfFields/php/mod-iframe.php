<?php

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group([
        'key' => 'group_56c47016ea9d5',
        'title' => __('Iframe settings', 'municipio'),
        'fields' => [
            0 => [
                'key' => 'field_56c4701d32cb4',
                'label' => __('Iframe URL', 'municipio'),
                'name' => 'iframe_url',
                'type' => 'url',
                'instructions' => __(
                    '<span style="color: #f00;">Your iframe link must start with http<strong>s</strong>://. Links without this prefix will not display.</span>',
                    'municipio',
                ),
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => [
                    'width' => 80,
                    'class' => '',
                    'id' => '',
                ],
                'default_value' => '',
                'placeholder' => __('Enter your embed url', 'municipio'),
            ],
            1 => [
                'key' => 'field_56c4704f32cb5',
                'label' => __('Iframe height', 'municipio'),
                'name' => 'iframe_height',
                'type' => 'number',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => [
                    'width' => 20,
                    'class' => '',
                    'id' => '',
                ],
                'default_value' => 350,
                'min' => 100,
                'max' => 10000,
                'step' => 10,
                'placeholder' => '',
                'prepend' => '',
                'append' => __('pixels', 'municipio'),
                'readonly' => 0,
                'disabled' => 0,
            ],
            2 => [
                'key' => 'field_60d9ccff3a64e',
                'label' => __('Description', 'municipio'),
                'name' => 'iframe_description',
                'type' => 'text',
                'instructions' => __('Describe the contents of this Iframe (not shown).', 'municipio'),
                'required' => 1,
                'conditional_logic' => 0,
                'wrapper' => [
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ],
                'default_value' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'maxlength' => '',
            ],
        ],
        'location' => [
            0 => [
                0 => [
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'mod-iframe',
                ],
            ],
            1 => [
                0 => [
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/iframe',
                ],
            ],
            2 => [
                0 => [
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/iframe',
                ],
            ],
        ],
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => true,
        'description' => '',
    ]);
}
