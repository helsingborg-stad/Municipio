<?php

declare(strict_types=1);

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group([
        'key' => 'group_62816d604ae46',
        'title' => __('Divider', 'municipio'),
        'fields' => [
            0 => [
                'key' => 'field_628b5b97810fc',
                'label' => __('Title variant', 'municipio'),
                'name' => 'divider_title_variant',
                'type' => 'select',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => [
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ],
                'choices' => [
                    'h1' => __('H1', 'municipio'),
                    'h2' => __('H2', 'municipio'),
                    'h3' => __('H3', 'municipio'),
                    'h4' => __('H4', 'municipio'),
                ],
                'default_value' => __('h2', 'municipio'),
                'allow_null' => 0,
                'multiple' => 0,
                'ui' => 0,
                'return_format' => 'value',
                'ajax' => 0,
                'placeholder' => '',
            ],
        ],
        'location' => [
            0 => [
                0 => [
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'mod-divider',
                ],
            ],
            1 => [
                0 => [
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/divider',
                ],
            ],
        ],
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'left',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => true,
        'description' => '',
        'show_in_rest' => 0,
        'acfe_display_title' => '',
        'acfe_autosync' => [
            0 => 'json',
        ],
        'acfe_form' => 0,
        'acfe_meta' => '',
        'acfe_note' => '',
    ]);
}
