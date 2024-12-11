<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_67597150948c7',
    'title' => __('SSO Settings', 'municipio'),
    'fields' => array(
        0 => array(
            'key' => 'field_67597153d37f3',
            'label' => __('Require SSO Login', 'municipio'),
            'name' => 'municipio_require_sso_login',
            'aria-label' => '',
            'type' => 'true_false',
            'instructions' => __('This feature disables the manual login ability. All attempts to login will be redirected to the IdP configured with MiniOrange SSO.', 'municipio'),
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'message' => __('* Requires MiniOrange SSO', 'municipio'),
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
                'value' => 'login-logout',
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