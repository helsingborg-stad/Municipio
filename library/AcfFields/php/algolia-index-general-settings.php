<?php

declare(strict_types=1);


if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group([
        'key' => 'group_68bfb24b7a4a2',
        'title' => __('General Settings', 'municipio'),
        'fields' => [
            0 => [
                'key' => 'field_68bfb24b6982b',
                'label' => __('Search Provider', 'municipio'),
                'name' => 'algolia_index_search_provider',
                'aria-label' => '',
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
                    'algolia' => __('Algolia', 'municipio'),
                ],
                'default_value' => __('algolia', 'municipio'),
                'return_format' => 'value',
                'multiple' => 0,
                'allow_null' => 0,
                'allow_in_bindings' => 0,
                'ui' => 0,
                'ajax' => 0,
                'placeholder' => '',
            ],
        ],
        'location' => [
            0 => [
                0 => [
                    'param' => 'options_page',
                    'operator' => '==',
                    'value' => 'municipio-settings',
                ],
            ],
        ],
        'menu_order' => -1,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => true,
        'description' => '',
        'show_in_rest' => 0,
    ]);
}
