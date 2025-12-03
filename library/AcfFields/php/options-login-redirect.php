<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_675aecfbf2f3d',
    'title' => __('Broken Links', 'municipio'),
    'fields' => array(
        0 => array(
            'key' => 'field_675aecfc0707a',
            'label' => __('Autologin', 'municipio'),
            'name' => 'municipio_redirect_to_login_when_internal_context',
            'aria-label' => '',
            'type' => 'true_false',
            'instructions' => __('This feature will redirect the user to the login page, if an internal context has been detected.', 'municipio'),
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'message' => __('* Requires Broken Links Detector Plugin', 'municipio'),
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