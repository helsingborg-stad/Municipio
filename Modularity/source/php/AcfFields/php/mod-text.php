<?php

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group([
        'key' => 'group_5891b49127038',
        'title' => __('Text options', 'municipio'),
        'fields' => [
            0 => [
                'key' => 'field_5891b6038c120',
                'label' => __('Göm textbox', 'municipio'),
                'name' => 'hide_box_frame',
                'type' => 'true_false',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => [
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ],
                'default_value' => 0,
                'message' => __('Ja, göm textbox (visa text som artikel)', 'municipio'),
                'ui' => 0,
                'ui_on_text' => '',
                'ui_off_text' => '',
            ],
        ],
        'location' => [
            0 => [
                0 => [
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'mod-text',
                ],
            ],
            1 => [
                0 => [
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/text',
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
