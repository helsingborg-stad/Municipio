<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_6123844e04276',
    'title' => __('Quicklinks menu', 'municipio'),
    'fields' => array(
        0 => array(
            'key' => 'field_6123844e0f0bb',
            'label' => __('Background Color', 'municipio'),
            'name' => 'quicklinks_background_color',
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
            'filter_context' => 'site.quicklinks',
            'share_option' => 0,
            'choices' => array(
                'white' => __('White (transparent)', 'municipio'),
                'primary' => __('Primary', 'municipio'),
                'secondary' => __('Secondary', 'municipio'),
                'tertiary' => __('Tertiary', 'municipio'),
            ),
            'default_value' => __('white', 'municipio'),
            'allow_null' => 0,
            'multiple' => 0,
            'ui' => 0,
            'return_format' => 'value',
            'ajax' => 0,
            'placeholder' => '',
        ),
        1 => array(
            'key' => 'field_6127571bcc76e',
            'label' => __('Text Color', 'municipio'),
            'name' => 'quicklinks_text_color',
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
            'filter_context' => 'site.quicklinks',
            'share_option' => 0,
            'choices' => array(
                'text-white' => __('White', 'municipio'),
                'text-black' => __('Black', 'municipio'),
            ),
            'default_value' => __('text-black', 'municipio'),
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
                'value' => 'quicklinks',
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