<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_6141a604364cd',
    'title' => __('Customizer configuration missing', 'municipio'),
    'fields' => array(
        0 => array(
            'key' => 'field_6141a613e20e5',
            'label' => __('Configuration Missing', 'municipio'),
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
            'message' => __('There are no fields connected to this section yet. Configure some fields to get going.', 'municipio'),
            'new_lines' => 'wpautop',
            'esc_html' => 1,
        ),
    ),
    'location' => array(
        0 => array(
            0 => array(
                'param' => 'customizer',
                'operator' => '==',
                'value' => 'general',
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