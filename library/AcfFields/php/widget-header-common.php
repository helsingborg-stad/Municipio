<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_5a65d5e7e913b',
    'title' => __('Widget header - Common', 'municipio'),
    'fields' => array(
        0 => array(
            'key' => 'field_5a65d5f15bffd',
            'label' => __('Visibility', 'municipio'),
            'name' => 'widget_header_visibility',
            'type' => 'checkbox',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'choices' => array(
                'xs' => __('Hide on extra small devices (XS)', 'municipio'),
                'sm' => __('Hide on small devices (SM)', 'municipio'),
                'md' => __('Hide on medium devices (MD)', 'municipio'),
                'lg' => __('Hide on large devices (LG)', 'municipio'),
            ),
            'allow_custom' => 0,
            'save_custom' => 0,
            'default_value' => array(
            ),
            'layout' => 'vertical',
            'toggle' => 0,
            'return_format' => 'value',
        ),
    ),
    'location' => array(
        0 => array(
            0 => array(
                'param' => 'post_type',
                'operator' => '==',
                'value' => 'post',
            ),
        ),
    ),
    'menu_order' => 0,
    'position' => 'normal',
    'style' => 'default',
    'label_placement' => 'top',
    'instruction_placement' => 'label',
    'hide_on_screen' => '',
    'active' => 0,
    'description' => '',
));
}