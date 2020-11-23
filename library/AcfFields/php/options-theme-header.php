<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_56a22a9c78e54',
    'title' => __('Header', 'municipio'),
    'fields' => array(
        0 => array(
            'key' => 'field_56a22aaa83835',
            'label' => __('Layout', 'municipio'),
            'name' => 'header_layout',
            'type' => 'radio',
            'instructions' => '',
            'required' => 1,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'choices' => array(
                'business' => __('Business (default)', 'municipio'),
                'casual' => __('Casual', 'municipio'),
            ),
            'allow_null' => 0,
            'other_choice' => 0,
            'default_value' => 'business',
            'layout' => 'vertical',
            'return_format' => 'value',
            'save_other_choice' => 0,
        ),
        1 => array(
            'key' => 'field_56a22aaa83835wef',
            'label' => __('Header content color', 'municipio'),
            'name' => 'header_content_color',
            'type' => 'radio',
            'instructions' => '',
            'required' => 1,
            'conditional_logic' => array(
                0 => array(
                    0 => array(
                        'field' => 'field_58737dd1dc763',
                        'operator' => '==',
                        'value' => 1,
                    ),
                ),
            ),
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'layout' => 'vertical',
            'choices' => array(
                'light' => __('Light', 'municipio'),
                'dark' => __('Dark', 'municipio'),
            ),
            'default_value' => 'light',
            'other_choice' => 0,
            'save_other_choice' => 0,
            'allow_null' => 0,
            'return_format' => 'value',
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