<?php

declare(strict_types=1);

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group([
        'key' => 'group_58ecb6b6330f4',
        'title' => __('Sites', 'municipio'),
        'fields' => [
            0 => [
                'default_value' => 0,
                'message' => __(__('Include main site', 'municipio'), 'modularity'),
                'ui' => 0,
                'ui_on_text' => '',
                'ui_off_text' => '',
                'key' => 'field_58ecb6bc4fcce',
                'label' => __(__('Include main site', 'municipio'), 'modularity'),
                'name' => 'include_main_site',
                'type' => 'true_false',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => [
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ],
            ],
        ],
        'location' => [
            0 => [
                0 => [
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'mod-sites',
                ],
            ],
        ],
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => 1,
        'description' => '',
    ]);
}
