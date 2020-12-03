<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_56a72f6430912',
    'title' => 'Display settings',
    'fields' => array(
        0 => array(
            'key' => 'field_56a72f9b645b7',
            'label' => __('Visa sök', 'municipio'),
            'name' => 'search_display',
            'type' => 'checkbox',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'layout' => 'vertical',
            'choices' => array(
                'hero' => __('Hero på startsidan', 'municipio'),
                'header_sub' => __('Headern på undersidor', 'municipio'),
                'header' => __('Headern på startsidan', 'municipio'),
                'mainmenu' => __('Alternativ i huvudmenyn', 'municipio'),
            ),
            'default_value' => array(
            ),
            'allow_custom' => 0,
            'save_custom' => 0,
            'toggle' => 0,
            'return_format' => 'value',
        ),
        1 => array(
            'key' => 'field_5c0fb3bb76405',
            'label' => __('Meddelande för tomt resultat', 'municipio'),
            'name' => 'empty_search_result_message',
            'type' => 'textarea',
            'instructions' => __('Lägg till meddelande när sökning inte returnerar något resultat.', 'municipio'),
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'default_value' => '',
            'placeholder' => __('Lägg till meddelande…', 'municipio'),
            'maxlength' => '',
            'rows' => '',
            'new_lines' => '',
        ),
    ),
    'location' => array(
        0 => array(
            0 => array(
                'param' => 'options_page',
                'operator' => '==',
                'value' => 'acf-options-search',
            ),
        ),
    ),
    'menu_order' => -10,
    'position' => 'normal',
    'style' => 'default',
    'label_placement' => 'top',
    'instruction_placement' => 'label',
    'hide_on_screen' => '',
    'active' => true,
    'description' => '',
));
}