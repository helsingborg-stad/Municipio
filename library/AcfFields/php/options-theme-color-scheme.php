<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_56a0a7dcb5c09',
    'title' => __('Color scheme', 'municipio'),
    'fields' => array(
        0 => array(
            'key' => 'field_56a0a7e36365b',
            'label' => __('Color scheme', 'municipio'),
            'name' => 'color_scheme',
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
    ),
    'location' => array(
        0 => array(
            0 => array(
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