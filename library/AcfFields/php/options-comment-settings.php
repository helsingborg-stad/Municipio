<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_67173bdc92fde',
    'title' => __('Comment Settings', 'municipio'),
    'fields' => array(
        0 => array(
            'key' => 'field_67175e844ab4a',
            'label' => __('Disable discussion feature', 'municipio'),
            'name' => 'disable_discussion_feature',
            'aria-label' => '',
            'type' => 'true_false',
            'instructions' => __('Disable discussion settings page, comment form and posted comments for all post types.', 'municipio'),
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'message' => '',
            'default_value' => 0,
            'ui_on_text' => __('Disabled', 'municipio'),
            'ui_off_text' => '',
            'ui' => 1,
        ),
        1 => array(
            'key' => 'field_67173bdc8a308',
            'label' => __('Hide discussion for logged out users', 'municipio'),
            'name' => 'hide_discussion_for_logged_out_users',
            'aria-label' => '',
            'type' => 'true_false',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => array(
                0 => array(
                    0 => array(
                        'field' => 'field_67175e844ab4a',
                        'operator' => '!=',
                        'value' => '1',
                    ),
                ),
            ),
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'message' => '',
            'default_value' => 0,
            'ui_on_text' => '',
            'ui_off_text' => '',
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
    'acfe_autosync' => '',
    'acfe_form' => 0,
    'acfe_meta' => '',
    'acfe_note' => '',
));
}