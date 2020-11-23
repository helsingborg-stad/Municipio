<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_584923bd30bfe',
    'title' => __('Site sub-title', 'municipio'),
    'fields' => array(
        0 => array(
            'key' => 'field_584923f324a08',
            'label' => '',
            'name' => '',
            'type' => 'message',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'message' => __('The sub site title can be used to complement a logotype', 'municipio'),
            'esc_html' => 0,
            'new_lines' => 'wpautop',
        ),
        1 => array(
            'key' => 'field_584923ca24a07',
            'label' => __('Sub-title', 'municipio'),
            'name' => 'sub_site_title',
            'type' => 'text',
            'instructions' => __('HTML allowed', 'municipio'),
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'default_value' => '',
            'maxlength' => '',
            'placeholder' => '',
            'prepend' => '',
            'append' => '',
        ),
    ),
    'location' => array(
        0 => array(
            0 => array(
                'param' => 'options_page',
                'operator' => '==',
                'value' => 'acf-options-header',
            ),
        ),
    ),
    'menu_order' => 0,
    'position' => 'normal',
    'style' => 'default',
    'label_placement' => 'top',
    'instruction_placement' => 'label',
    'hide_on_screen' => '',
    'active' => true,
    'description' => '',
));
}