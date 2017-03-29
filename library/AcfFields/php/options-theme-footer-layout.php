<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_56e804931bd1e',
    'title' => 'Footer layout',
    'fields' => array(
        0 => array(
            'multiple' => 0,
            'allow_null' => 0,
            'choices' => array(
                'default' => __('Default style', 'municipio'),
                'compressed' => __('Compressed', 'municipio'),
            ),
            'default_value' => array(
                0 => 'default',
            ),
            'ui' => 0,
            'ajax' => 0,
            'placeholder' => '',
            'return_format' => 'value',
            'key' => 'field_5710cd81d4a19',
            'label' => __('Footer layout', 'municipio'),
            'name' => 'footer_layout',
            'type' => 'select',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'disabled' => 0,
            'readonly' => 0,
        ),
        1 => array(
            'default_value' => 0,
            'message' => __('Enable footer signature', 'municipio'),
            'ui' => 0,
            'ui_on_text' => '',
            'ui_off_text' => '',
            'key' => 'field_56e8050730333',
            'label' => __('Footer signature', 'municipio'),
            'name' => 'footer_signature_show',
            'type' => 'true_false',
            'instructions' => __('If enabled a predefined footer signature logo will be appended to the footer.', 'municipio'),
            'required' => 0,
            'conditional_logic' => 0,
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
                'value' => 'acf-options-footer',
            ),
        ),
    ),
    'menu_order' => -100,
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