<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_6141cc9c72cc3',
    'title' => __('Language Menu', 'municipio'),
    'fields' => array(
        0 => array(
            'key' => 'field_614449a2d4489',
            'label' => __('Number of Menu Items', 'municipio'),
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
            'message' => __('We recommend limiting preselected translations to four', 'municipio'),
            'new_lines' => 'wpautop',
            'esc_html' => 0,
        ),
        1 => array(
            'key' => 'field_6141ccdf9d7ef',
            'label' => __('Disclaimer', 'municipio'),
            'name' => 'language_menu_disclaimer',
            'type' => 'textarea',
            'instructions' => __('A disclaimer to be shown below language options', 'municipio'),
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'default_value' => '',
            'placeholder' => '',
            'maxlength' => '',
            'rows' => '',
            'new_lines' => '',
        ),
        2 => array(
            'key' => 'field_6141cd72ba87a',
            'label' => __('More Languages Link', 'municipio'),
            'name' => 'language_menu_more_languages',
            'type' => 'url',
            'instructions' => __('A link to the translation service where user can select translation language themself', 'municipio'),
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'default_value' => '',
            'placeholder' => '',
        ),
    ),
    'location' => array(
        0 => array(
            0 => array(
                'param' => 'nav_menu',
                'operator' => '==',
                'value' => 'location/language-menu',
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