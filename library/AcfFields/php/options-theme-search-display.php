<?php 


if (function_exists('acf_add_local_field_group')) {

    acf_add_local_field_group(array(
    'key' => 'group_56a72f6430912',
    'title' => __('Display settings', 'municipio'),
    'fields' => array(
        0 => array(
            'key' => 'field_56a72f9b645b7',
            'label' => __('Show search', 'municipio'),
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
            'render_type' => '',
            'filter_context' => '',
            'share_option' => 0,
            'choices' => array(
                'hero' => __('Hero on frontpage', 'municipio'),
                'header_sub' => __('Header on sub pages', 'municipio'),
                'header' => __('Header on frontpage', 'municipio'),
                'mainmenu' => __('Option in main menu', 'municipio'),
                'mobile' => __('Option in mobile menu', 'municipio'),
                'hamburger_menu' => __('Hamburger menu', 'municipio'),
            ),
            'allow_custom' => 0,
            'default_value' => array(
            ),
            'layout' => 'vertical',
            'toggle' => 0,
            'return_format' => 'value',
            'save_custom' => 0,
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