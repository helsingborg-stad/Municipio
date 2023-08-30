<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_63eb4a0aa476e',
    'title' => __('Location', 'visit'),
    'fields' => array(
        0 => array(
            'key' => 'field_63eb4a0b11678',
            'label' => __('Location', 'visit'),
            'name' => 'location',
            'aria-label' => '',
            'type' => 'google_map',
            'instructions' => __('Area or street address of the location.', 'visit'),
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'acfe_save_meta' => 0,
            'center_lat' => '56.042834',
            'center_lng' => '12.7009283',
            'zoom' => 11,
            'height' => 300,
        ),
    ),
    'location' => array(
        0 => array(
            0 => array(
                'param' => 'content_type',
                'operator' => '==',
                'value' => 'place',
            ),
        ),
    ),
    'menu_order' => 0,
    'position' => 'normal',
    'style' => 'default',
    'label_placement' => 'left',
    'instruction_placement' => 'label',
    'hide_on_screen' => '',
    'active' => true,
    'description' => '',
    'show_in_rest' => 0,
    'acfe_display_title' => '',
    'acfe_autosync' => '',
    'acfe_form' => 0,
    'acfe_meta' => '',
    'acfe_note' => '',
));
}