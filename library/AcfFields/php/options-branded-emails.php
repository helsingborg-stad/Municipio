<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_6639e9aa1409f',
    'title' => __('Branded E-mails', 'municipio'),
    'fields' => array(
        0 => array(
            'key' => 'field_6639e9abdbe59',
            'label' => __('Enable branded e-mails', 'municipio'),
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
        1 => array(
            'key' => 'field_663a1a9ae9398',
            'label' => __('Mail from', 'municipio'),
            'name' => 'mun_branded_emails_get_email_from',
            'aria-label' => '',
            'type' => 'email',
            'instructions' => __('Controls the sender e-mail address for outgoing e-mails.', 'municipio'),
            'required' => 0,
            'conditional_logic' => array(
                0 => array(
                    0 => array(
                        'field' => 'field_6639e9abdbe59',
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
        ),
        2 => array(
            'key' => 'field_663a1ae0e9399',
            'label' => __('From name', 'municipio'),
            'name' => 'mun_branded_emails_get_email_from_name',
            'aria-label' => '',
            'type' => 'text',
            'instructions' => __('Controls the sender name for outgoing e-mails.', 'municipio'),
            'required' => 0,
            'conditional_logic' => array(
                0 => array(
                    0 => array(
                        'field' => 'field_6639e9abdbe59',
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
            'maxlength' => '',
            'placeholder' => '',
            'prepend' => '',
            'append' => '',
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