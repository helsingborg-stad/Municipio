<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_614337104a06d',
    'title' => __('Text', 'municipio'),
    'fields' => array(
        0 => array(
            'key' => 'field_60631b4025918',
            'label' => __('Text - Box - Style', 'municipio'),
            'name' => 'script-box-style',
            'type' => 'select',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'render_type' => 'filter',
            'filter_context' => 'module.text.box',
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
                'value' => 'text',
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