<?php

declare(strict_types=1);

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group([
        'key' => 'group_58eb9450b0a9f',
        'title' => __('Easy reading settings', 'municipio'),
        'fields' => [
            0 => [
                'key' => 'field_58eb9486a647a',
                'label' => __('Post types', 'municipio'),
                'name' => 'easy_reading_posttypes',
                'aria-label' => '',
                'type' => 'select',
                'instructions' => __('Show easy reading field on selected post types.', 'municipio'),
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => [
                    'width' => '100',
                    'class' => '',
                    'id' => '',
                ],
                'choices' => [],
                'default_value' => false,
                'return_format' => 'value',
                'allow_null' => 0,
                'multiple' => 1,
                'ui' => 1,
                'ajax' => 0,
                'allow_custom' => 0,
                'search_placeholder' => '',
                'placeholder' => '',
                'disabled' => 0,
                'readonly' => 0,
            ],
        ],
        'location' => [
            0 => [
                0 => [
                    'param' => 'options_page',
                    'operator' => '==',
                    'value' => 'easy-reading-options',
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
        'show_in_rest' => false,
        'display_title' => '',
        'allow_ai_access' => false,
        'ai_description' => '',
    ]);
}
