<?php

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_56d83cff12bb3',
    'title' => 'Navigation settings',
    'fields' => array(
        0 => array(
            'default_value' => 0,
            'message' => __('Hide', 'municipio'),
            'ui' => 0,
            'ui_on_text' => '',
            'ui_off_text' => '',
            'key' => 'field_56d83d2777785',
            'label' => __('Hide from menu', 'municipio'),
            'name' => 'hide_in_menu',
            'type' => 'true_false',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
        ),
        1 => array(
            'default_value' => '',
            'maxlength' => '',
            'placeholder' => '',
            'prepend' => '',
            'append' => '',
            'key' => 'field_56d83d4e77786',
            'label' => __('Menu title', 'municipio'),
            'name' => 'custom_menu_title',
            'type' => 'text',
            'instructions' => __('If you want to use another title for this page in the sidebar navigation fill it in here. If you want to use the page title just leave this field empty.', 'municipio'),
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'readonly' => 0,
            'disabled' => 0,
        ),
    ),
    'location' => array(
        0 => array(
            0 => array(
                'param' => 'post_type',
                'operator' => '!=',
                'value' => 'null',
            ),
        ),
    ),
    'menu_order' => 0,
    'position' => 'side',
    'style' => 'default',
    'label_placement' => 'top',
    'instruction_placement' => 'label',
    'hide_on_screen' => '',
    'active' => 1,
    'description' => '',
    'local' => 'php',
));
}
