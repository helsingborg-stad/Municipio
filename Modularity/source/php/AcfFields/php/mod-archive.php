<?php 


if (function_exists('acf_add_local_field_group')) {

    acf_add_local_field_group(array(
    'key' => 'group_6900b82ba2c5d',
    'title' => __('Archive module', 'municipio'),
    'fields' => array(
        0 => array(
            'key' => 'field_6900b82bd650a',
            'label' => __('Test', 'municipio'),
            'name' => 'test',
            'aria-label' => '',
            'type' => 'text',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'default_value' => '',
            'maxlength' => '',
            'allow_in_bindings' => 0,
            'placeholder' => '',
            'prepend' => '',
            'append' => '',
        ),
    ),
    'location'              => array(
       0 => array(
           0 => array(
               'param'    => 'post_type',
               'operator' => '==',
               'value'    => 'mod-archive',
           ),
       ),
       1 => array(
           0 => array(
               'param'    => 'block',
               'operator' => '==',
               'value'    => 'acf/archive',
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
    'show_in_rest' => 0,
    'display_title' => '',
));

}