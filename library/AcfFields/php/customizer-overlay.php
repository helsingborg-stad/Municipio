<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_615c1bc375e8e',
    'title' => __('Overlay', 'municipio'),
    'fields' => array(
        0 => array(
            'key' => 'field_615c1bc3772c6',
            'label' => __('Overlay colour', 'municipio'),
            'name' => 'municipio_color_general_overlay',
            'type' => 'group',
            'instructions' => __('Set a overlay, the color will be overlayed on images.', 'municipio'),
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'render_type' => 'var_colorgroup',
            'filter_context' => '',
            'share_option' => 0,
            'layout' => 'block',
            'sub_fields' => array(
                0 => array(
                    'key' => 'field_615c1bc3780b0',
                    'label' => __('Color', 'municipio'),
                    'name' => 'color',
                    'type' => 'color_picker',
                    'instructions' => '',
                    'required' => 1,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'render_type' => '',
                    'filter_context' => '',
                    'share_option' => 0,
                    'default_value' => __('#000000', 'municipio'),
                    'enable_opacity' => false,
                    'return_format' => 'string',
                ),
                1 => array(
                    'key' => 'field_615c1bc3780b6',
                    'label' => __('Alpha', 'municipio'),
                    'name' => 'alpha',
                    'type' => 'range',
                    'instructions' => '',
                    'required' => 1,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'render_type' => '',
                    'filter_context' => '',
                    'share_option' => 0,
                    'default_value' => 75,
                    'min' => 0,
                    'max' => 100,
                    'step' => 1,
                    'prepend' => '',
                    'append' => '',
                ),
            ),
        ),
    ),
    'location' => array(
        0 => array(
            0 => array(
                'param' => 'customizer',
                'operator' => '==',
                'value' => 'overlay',
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