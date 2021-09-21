<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_6143370287209',
    'title' => __('Map', 'municipio'),
    'fields' => array(
        0 => array(
            'key' => 'field_6063013a5068c',
            'label' => __('Map - Style', 'municipio'),
            'name' => 'map-style',
            'type' => 'select',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'render_type' => '',
            'filter_context' => 'module.map',
            'share_option' => 1,
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
                'value' => 'map',
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