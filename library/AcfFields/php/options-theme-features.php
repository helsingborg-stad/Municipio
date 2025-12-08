<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_6639e9aa1409f',
    'title' => __('Theme features', 'municipio'),
    'fields' => array(
        0 => array(
            'key' => 'field_6639e9abdbe59',
            'label' => __('Branded e-mails', 'municipio'),
            'name' => 'mun_branded_emails_enabled',
            'aria-label' => '',
            'type' => 'true_false',
            'instructions' => __('All e-mails sent from WordPress will be branded using the theme appearance settings.', 'municipio'),
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'message' => '',
            'default_value' => 0,
            'ui_on_text' => __('Enabled', 'municipio'),
            'ui_off_text' => __('Disabled', 'municipio'),
            'ui' => 1,
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