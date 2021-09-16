<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_6143379386443',
    'title' => __('Form', 'municipio'),
    'fields' => array(
        0 => array(
            'repeater_choices' => false,
            'repeater_field' => '',
            'repeater_label_field' => '',
            'repeater_value_field' => '',
            'repeater_post_id' => 0,
            'repeater_display_value' => 0,
            'key' => 'field_6064452900410',
            'label' => __('Form - Style', 'municipio'),
            'name' => 'form',
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
                'none' => __('None', 'municipio'),
                'panel' => __('Panel', 'municipio'),
                'accented' => __('Accented', 'municipio'),
                'highlight' => __('Highlight', 'municipio'),
            ),
            'default_value' => false,
            'allow_null' => 0,
            'multiple' => 0,
            'ui' => 1,
            'ajax' => 0,
            'return_format' => 'value',
            'placeholder' => '',
        ),
    ),
    'location' => array(
        0 => array(
            0 => array(
                'param' => 'customizer',
                'operator' => '==',
                'value' => 'form',
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