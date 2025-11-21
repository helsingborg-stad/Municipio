<?php

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group([
        'key' => 'group_67a6218f4b8a6',
        'title' => __('Interactive Map', 'municipio'),
        'fields' => [
            0 => [
                'key' => 'field_67dd6e61f1d7d',
                'label' => __('Size', 'municipio'),
                'name' => 'mod_interactive_map_size',
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
                    'small' => __('Small', 'municipio'),
                    'medium' => __('Medium', 'municipio'),
                    'large' => __('Large', 'municipio'),
                ],
                'default_value' => __('medium', 'municipio'),
                'return_format' => 'value',
                'multiple' => 0,
                'allow_null' => 0,
                'allow_in_bindings' => 0,
                'ui' => 0,
                'ajax' => 0,
                'placeholder' => '',
                'allow_custom' => 0,
                'search_placeholder' => '',
            ],
            1 => [
                'key' => 'field_67b44cbc181c6',
                'label' => __('Interactive Map', 'municipio'),
                'name' => 'interactive-map',
                'aria-label' => '',
                'type' => 'openstreetmap',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => [
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ],
                'default_lat' => '',
                'default_lng' => '',
                'allow_in_bindings' => 1,
            ],
        ],
        'location' => [
            0 => [
                0 => [
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'mod-interactivemap',
                ],
            ],
            1 => [
                0 => [
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/interactivemap',
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
