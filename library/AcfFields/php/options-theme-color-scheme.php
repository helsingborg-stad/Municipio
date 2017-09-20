<?php 
if (function_exists('acf_add_local_field_group')) {

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_56a0a7dcb5c09',
    'title' => 'Color scheme',
    'fields' => array(
        0 => array(
            'layout' => 'vertical',
            'choices' => array(
                'gray' => 'Gray',
                'red' => 'Red',
                'blue' => 'Blue',
                'green' => 'Green',
                'purple' => 'Purple',
                'familjen' => 'Familjen helsingborg',
                'astorp' => 'Åstorps kommun',
                'hultsfred' => 'Hultsfreds kommun',
            ),
            'default_value' => 'gray',
            'other_choice' => 0,
            'save_other_choice' => 0,
            'allow_null' => 0,
            'return_format' => 'value',
            'key' => 'field_56a0a7e36365b',
            'label' => 'Color scheme',
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
    'local' => 'php',
));
}