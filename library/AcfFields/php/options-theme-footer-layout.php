<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_56e804931bd1e',
    'title' => __('Footer layout', 'municipio'),
    'fields' => array(
        0 => array(
            'key' => 'field_5b44d40efbb2d',
            'label' => __('Layout', 'municipio'),
            'name' => 'footer_layout',
            'type' => 'radio',
            'instructions' => '',
            'required' => 1,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'choices' => array(
                'default' => __('Default', 'municipio'),
            ),
            'allow_null' => 0,
            'other_choice' => 0,
            'save_other_choice' => 0,
            'default_value' => '',
            'layout' => 'vertical',
            'return_format' => 'value',
        ),
        1 => array(
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
            'default_value' => 0,
            'message' => __('Enable footer signature', 'municipio'),
            'ui' => 0,
            'ui_on_text' => '',
            'ui_off_text' => '',
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
));
}