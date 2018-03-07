<?php

if (function_exists('acf_add_local_field_group')) {
acf_add_local_field_group(array(
    'key' => 'group_56a0a7dcb5c09',
    'title' => __('Color scheme', 'municipio'),
    'fields' => array(
        array(
            'key' => 'field_5a9945a41d637',
            'label' => __('Custom color scheme', 'municipio'),
            'name' => 'custom_color_scheme',
            'type' => 'true_false',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'message' => __('Use custom color scheme', 'municipio'),
            'default_value' => 0,
            'ui' => 0,
            'ui_on_text' => '',
            'ui_off_text' => '',
        ),
        array(
            'key' => 'field_56a0a7e36365b',
            'label' => __('Color scheme', 'municipio'),
            'name' => 'color_scheme',
            'type' => 'radio',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => array(
                array(
                    array(
                        'field' => 'field_5a9945a41d637',
                        'operator' => '!=',
                        'value' => '1',
                    ),
                ),
            ),
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'choices' => array(
                'gray' => __('Gray', 'municipio'),
                'red' => __('Red', 'municipio'),
                'blue' => __('Blue', 'municipio'),
                'green' => __('Green', 'municipio'),
                'purple' => __('Purple', 'municipio'),
                'familjen' => __('Familjen helsingborg', 'municipio'),
                'astorp' => __('Åstorps kommun', 'municipio'),
                'hultsfred' => __('Hultsfreds kommun', 'municipio'),
            ),
            'allow_null' => 0,
            'other_choice' => 0,
            'save_other_choice' => 0,
            'default_value' => 'gray',
            'layout' => 'vertical',
            'return_format' => 'value',
        ),
        array(
            'key' => 'field_5a9946401d638',
            'label' => __('Color scheme', 'municipio'),
            'name' => 'color_scheme',
            'type' => 'text',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => array(
                array(
                    array(
                        'field' => 'field_5a9945a41d637',
                        'operator' => '==',
                        'value' => '1',
                    ),
                ),
            ),
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'default_value' => '',
            'placeholder' => '',
            'prepend' => '',
            'append' => '',
            'maxlength' => '',
        ),
    ),
    'location' => array(
        array(
            array(
                'param' => 'options_page',
                'operator' => '==',
                'value' => 'acf-options-theme-options',
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
