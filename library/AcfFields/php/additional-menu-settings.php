<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_66e05ac66c932',
    'title' => __('Additional menu settings', 'municipio'),
    'fields' => array(
        0 => array(
            'key' => 'field_66e05ac6505bf',
            'label' => __('Add menu to additional location', 'municipio'),
            'name' => 'menu_location',
            'aria-label' => '',
            'type' => 'checkbox',
            'instructions' => __('This allows you to add this menu to another already existing menu.', 'municipio'),
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'choices' => array(
            ),
            'default_value' => array(
            ),
            'return_format' => 'value',
            'allow_custom' => 0,
            'layout' => 'vertical',
            'toggle' => 0,
            'save_custom' => 0,
            'custom_choice_button_text' => 'Add new choice',
        ),
    ),
    'location' => array(
        0 => array(
            0 => array(
                'param' => 'nav_menu',
                'operator' => '==',
                'value' => 'all',
            ),
        ),
    ),
    'menu_order' => 0,
    'position' => 'normal',
    'style' => 'default',
    'label_placement' => 'left',
    'instruction_placement' => 'label',
    'hide_on_screen' => '',
    'active' => true,
    'description' => '',
    'show_in_rest' => 0,
    'acfe_display_title' => '',
    'acfe_autosync' => array(
        0 => 'json',
    ),
    'acfe_form' => 0,
    'acfe_meta' => '',
    'acfe_note' => '',
));
}