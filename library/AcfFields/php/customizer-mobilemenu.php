<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_6143458359420',
    'title' => __('Mobile menu', 'municipio'),
    'fields' => array(
        0 => array(
            'repeater_choices' => false,
            'repeater_field' => '',
            'repeater_label_field' => '',
            'repeater_value_field' => '',
            'repeater_post_id' => 0,
            'repeater_display_value' => 0,
            'key' => 'field_61126702da36c',
            'label' => __('Mobile menu style', 'municipio'),
            'name' => 'mobile_menu_style',
            'type' => 'select',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'choices' => array(
                'monotone' => __('Monotone', 'municipio'),
                'duotone' => __('Duotone', 'municipio'),
            ),
            'default_value' => __('monotone', 'municipio'),
            'allow_null' => 0,
            'multiple' => 0,
            'ui' => 0,
            'return_format' => 'value',
            'ajax' => 0,
            'placeholder' => '',
        ),
    ),
    'location' => array(
        0 => array(
            0 => array(
                'param' => 'customizer',
                'operator' => '==',
                'value' => 'mobilemenu',
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