<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_57469ceda9387',
    'title' => __('Intranet news', 'municipio-intranet'),
    'fields' => array(
        0 => array(
            'layout' => 'vertical',
            'choices' => array(
                'network_subscribed' => __('Combine news from subscribed networks', 'municipio-intranet'),
                'network' => __('Combine news from all network', 'municipio-intranet'),
                'blog' => __('Only news from the current site', 'municipio-intranet'),
            ),
            'default_value' => 'network_subscribed',
            'other_choice' => 0,
            'save_other_choice' => 0,
            'allow_null' => 0,
            'return_format' => 'value',
            'key' => 'field_57469cf4d2d8e',
            'label' => __('Display', 'municipio-intranet'),
            'name' => 'display',
            'type' => 'radio',
            'instructions' => '',
            'required' => 1,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
        ),
        1 => array(
            'default_value' => 10,
            'min' => '',
            'max' => '',
            'step' => '',
            'placeholder' => '',
            'prepend' => '',
            'append' => '',
            'key' => 'field_5746a4a2fbfdb',
            'label' => __('Number of news', 'municipio-intranet'),
            'name' => 'limit',
            'type' => 'number',
            'instructions' => __('Number of news to display', 'municipio-intranet'),
            'required' => 1,
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
                'operator' => '==',
                'value' => 'mod-intranet-news',
            ),
        ),
    ),
    'menu_order' => 0,
    'position' => 'normal',
    'style' => 'default',
    'label_placement' => 'top',
    'instruction_placement' => 'label',
    'hide_on_screen' => '',
    'active' => 1,
    'description' => '',
));
}