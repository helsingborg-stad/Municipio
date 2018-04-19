<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_5a58ce68e8b61',
    'title' => __('Widget header - Menu', 'municipio'),
    'fields' => array(
        0 => array(
            'key' => 'field_5a58ce7a48379',
            'label' => __('Select WP menu', 'municipio'),
            'name' => 'widget_header_menu',
            'type' => 'select',
            'instructions' => '',
            'required' => 1,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'choices' => array(
                2 => __('Menu (Menu ID: 2)', 'municipio'),
                6 => __('topmeny (Menu ID: 6)', 'municipio'),
            ),
            'default_value' => array(
            ),
            'allow_null' => 0,
            'multiple' => 0,
            'ui' => 0,
            'ajax' => 0,
            'return_format' => 'value',
            'placeholder' => '',
        ),
        1 => array(
            'key' => 'field_5ad89441c02a1',
            'label' => __('Link size', 'municipio'),
            'name' => 'widget_link_size',
            'type' => 'radio',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'choices' => array(
                'default' => __('Default', 'municipio'),
                'small' => __('Small', 'municipio'),
                'large' => __('Large', 'municipio'),
            ),
            'allow_null' => 0,
            'other_choice' => 0,
            'save_other_choice' => 0,
            'default_value' => '',
            'layout' => 'horizontal',
            'return_format' => 'value',
        ),
    ),
    'location' => array(
        0 => array(
            0 => array(
                'param' => 'widget',
                'operator' => '==',
                'value' => 'widget-header-menu',
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
));
}