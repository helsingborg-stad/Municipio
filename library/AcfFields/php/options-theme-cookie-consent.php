<?php

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_56bc6b6466df1',
    'title' => 'Cookie consent',
    'fields' => array(
        0 => array(
            'default_value' => 1,
            'message' => __('Show cookie consent to users if not already accepted', 'municipio'),
            'ui' => 0,
            'ui_on_text' => '',
            'ui_off_text' => '',
            'key' => 'field_56bc6b7837da1',
            'label' => __('Active', 'municipio'),
            'name' => 'cookie_consent_active',
            'type' => 'true_false',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
        ),
        1 => array(
            'tabs' => 'all',
            'toolbar' => 'basic',
            'media_upload' => 0,
            'default_value' => __('This website uses cookies to ensure you get the best experience on our website.', 'municipio'),
            'delay' => 0,
            'key' => 'field_56bc6b9e37da2',
            'label' => __('Message', 'municipio'),
            'name' => 'cookie_consent_message',
            'type' => 'wysiwyg',
            'instructions' => '',
            'required' => 1,
            'conditional_logic' => array(
                0 => array(
                    0 => array(
                        'field' => 'field_56bc6b7837da1',
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
        ),
        2 => array(
            'default_value' => 'Got it!',
            'maxlength' => '',
            'placeholder' => '',
            'prepend' => '',
            'append' => '',
            'key' => 'field_56bc6bc937da3',
            'label' => __('Button', 'municipio'),
            'name' => 'cookie_consent_button',
            'type' => 'text',
            'instructions' => '',
            'required' => 1,
            'conditional_logic' => array(
                0 => array(
                    0 => array(
                        'field' => 'field_56bc6b7837da1',
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
            'readonly' => 0,
            'disabled' => 0,
        ),
        3 => array(
            'layout' => 'vertical',
            'choices' => array(
                'top' => __('At the top of the page', 'municipio'),
                'bottom-fixed' => __('Fixed to bottom of window', 'municipio'),
            ),
            'default_value' => '',
            'other_choice' => 0,
            'save_other_choice' => 0,
            'allow_null' => 0,
            'return_format' => 'value',
            'key' => 'field_56bc91a1aad5c',
            'label' => __('Placement', 'municipio'),
            'name' => 'cookie_consent_placement',
            'type' => 'radio',
            'instructions' => '',
            'required' => 1,
            'conditional_logic' => array(
                0 => array(
                    0 => array(
                        'field' => 'field_56bc6b7837da1',
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
    'label_placement' => 'top',
    'instruction_placement' => 'label',
    'hide_on_screen' => '',
    'active' => 1,
    'description' => '',
    'local' => 'php',
));
}
