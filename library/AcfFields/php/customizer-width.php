<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_60928d240f1bf',
    'title' => __('Widths', 'municipio'),
    'fields' => array(
        0 => array(
            'key' => 'field_614316f767fa4',
            'label' => __('Page Widths', 'municipio'),
            'name' => 'Page Widths',
            'type' => 'group',
            'instructions' => __('Set the maximum page withs of different page types.', 'municipio'),
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'render_type' => 'var',
            'layout' => 'block',
            'sub_fields' => array(
                0 => array(
                    'key' => 'field_609bdcc8348d6',
                    'label' => __('Default', 'municipio'),
                    'name' => 'municipio_container_width',
                    'type' => 'range',
                    'instructions' => '',
                    'required' => 1,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'render_type' => 'var',
                    'default_value' => 1280,
                    'min' => 900,
                    'max' => 1660,
                    'step' => 32,
                    'prepend' => '',
                    'append' => __('px', 'municipio'),
                ),
                1 => array(
                    'key' => 'field_60928f237c070',
                    'label' => __('Front Page', 'municipio'),
                    'name' => 'municipio_container_width_frontpage',
                    'type' => 'range',
                    'instructions' => '',
                    'required' => 1,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'render_type' => 'var',
                    'default_value' => 1280,
                    'min' => 900,
                    'max' => 1660,
                    'step' => 32,
                    'prepend' => '',
                    'append' => __('px', 'municipio'),
                ),
                2 => array(
                    'key' => 'field_609bdcad348d5',
                    'label' => __('Archives', 'municipio'),
                    'name' => 'municipio_container_width_archive',
                    'type' => 'range',
                    'instructions' => '',
                    'required' => 1,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'render_type' => 'var',
                    'default_value' => 1280,
                    'min' => 900,
                    'max' => 1660,
                    'step' => 32,
                    'prepend' => '',
                    'append' => __('px', 'municipio'),
                ),
                3 => array(
                    'key' => 'field_609298276e5b2',
                    'label' => __('Content area', 'municipio'),
                    'name' => 'municipio_container_width_content',
                    'type' => 'range',
                    'instructions' => '',
                    'required' => 1,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'render_type' => 'var',
                    'default_value' => 700,
                    'min' => 400,
                    'max' => 1000,
                    'step' => 32,
                    'prepend' => '',
                    'append' => __('px', 'municipio'),
                ),
            ),
        ),
        1 => array(
            'key' => 'field_61432dfbaa109',
            'label' => __('Column sizes', 'municipio'),
            'name' => 'column_sizes',
            'type' => 'group',
            'instructions' => __('Set the widths of columns to accomodate more versatile design layouts. The middle column will adjust to remaining space automatically.', 'municipio'),
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'render_type' => 'filter',
            'layout' => 'block',
            'sub_fields' => array(
                0 => array(
                    'repeater_choices' => 0,
                    'repeater_field' => '',
                    'repeater_label_field' => '',
                    'repeater_value_field' => '',
                    'repeater_post_id' => 0,
                    'repeater_display_value' => 0,
                    'key' => 'field_60d339b60049e',
                    'label' => __('Left', 'municipio'),
                    'name' => 'column_size_left',
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
                        'normal' => __('Normal', 'municipio'),
                        'large' => __('Large', 'municipio'),
                    ),
                    'default_value' => false,
                    'allow_null' => 0,
                    'multiple' => 0,
                    'ui' => 0,
                    'return_format' => 'value',
                    'ajax' => 0,
                    'placeholder' => '',
                ),
                1 => array(
                    'repeater_choices' => false,
                    'repeater_field' => '',
                    'repeater_label_field' => '',
                    'repeater_value_field' => '',
                    'repeater_post_id' => 0,
                    'repeater_display_value' => 0,
                    'key' => 'field_60d3393d1231a',
                    'label' => __('Right', 'municipio'),
                    'name' => 'column_size_right',
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
                        'normal' => __('Normal', 'municipio'),
                        'large' => __('Large', 'municipio'),
                    ),
                    'default_value' => __('normal', 'municipio'),
                    'allow_null' => 0,
                    'multiple' => 0,
                    'ui' => 0,
                    'return_format' => 'value',
                    'ajax' => 0,
                    'placeholder' => '',
                ),
            ),
        ),
    ),
    'location' => array(
        0 => array(
            0 => array(
                'param' => 'customizer',
                'operator' => '==',
                'value' => 'widths',
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