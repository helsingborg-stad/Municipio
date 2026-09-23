<?php

declare(strict_types=1);

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group([
        'key' => 'group_mod_negative_margin',
        'title' => __('Negative margin', 'municipio'),
        'fields' => [
            0 => [
                'key' => 'field_mod_negative_margin_amount',
                'label' => __('Amount of negative margin', 'municipio'),
                'name' => 'negative_margin_amount',
                'type' => 'range',
                'instructions' => __('The value uses the same 8-pixel scale as the Spacer module.', 'municipio'),
                'required' => 1,
                'conditional_logic' => 0,
                'wrapper' => [
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ],
                'default_value' => 4,
                'min' => 0,
                'max' => 24,
                'step' => 2,
                'prepend' => '',
                'append' => '',
            ],
        ],
        'location' => [
            0 => [
                0 => [
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'mod-negative-margin',
                ],
            ],
            1 => [
                0 => [
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/negative-margin',
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
