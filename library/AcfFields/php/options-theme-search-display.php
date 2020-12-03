<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_56a72f6430912',
    'title' => __('Display settings', 'municipio'),
    'fields' => array(
        0 => array(
            'key' => 'field_56a72f9b645b7',
            'label' => __('Display search', 'municipio'),
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
                'hero' => __('In front-page hero/slider', 'municipio'),
                'header_sub' => __('In the header of all pages but front page', 'municipio'),
                'header' => __('In the header of all pages', 'municipio'),
                'mainmenu' => __('Icon in main menu', 'municipio'),
            ),
            'default_value' => array(
            ),
            'allow_custom' => 0,
            'save_custom' => 0,
            'toggle' => 0,
            'return_format' => 'value',
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