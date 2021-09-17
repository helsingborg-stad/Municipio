<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_6143452439e4a',
    'title' => __('Header', 'municipio'),
    'fields' => array(
        0 => array(
            'key' => 'field_61434d8f49e64',
            'label' => __('Scroll behaviour', 'municipio'),
            'name' => '',
            'type' => 'message',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'render_type' => '',
            'share_option' => 0,
            'message' => __('Adjust how the header section should behave when the user scrolls trough the page.', 'municipio'),
            'new_lines' => 'wpautop',
            'esc_html' => 0,
        ),
        1 => array(
            'repeater_choices' => 0,
            'repeater_field' => '',
            'repeater_label_field' => '',
            'repeater_value_field' => '',
            'repeater_post_id' => 0,
            'repeater_display_value' => 0,
            'key' => 'field_61434d3478ef7',
            'label' => __('Sticky', 'municipio'),
            'name' => 'municipio_header_stick_to_top',
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
            'filter_context' => 'siteHeader',
            'share_option' => 1,
            'choices' => array(
                0 => __('None', 'municipio'),
                'sticky' => __('Stick to top', 'municipio'),
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
                'value' => 'header',
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