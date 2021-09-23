<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_614c7131c10e6',
    'title' => __('Hero', 'municipio'),
    'fields' => array(
        0 => array(
            'key' => 'field_614c713ae73ea',
            'label' => __('Vibrant overlay colour', 'municipio'),
            'name' => 'municipio_hero_overlay_color__vibrant',
            'type' => 'group',
            'instructions' => '',
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
                    'key' => 'field_614c7189e73eb',
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
                ),
                1 => array(
                    'key' => 'field_614c7197e73ec',
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
        1 => array(
            'key' => 'field_614c720fb65a4',
            'label' => __('Neutral overlay colour', 'municipio'),
            'name' => 'municipio_hero_overlay_color__neutral',
            'type' => 'group',
            'instructions' => '',
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
                    'key' => 'field_614c720fb65a5',
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
                ),
                1 => array(
                    'key' => 'field_614c720fb65a6',
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
                'value' => 'hero',
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