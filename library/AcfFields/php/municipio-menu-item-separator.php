<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_6645d22ca7ef8',
    'title' => __('Municipio Menu Item Separator', 'municipio'),
    'fields' => array(
        0 => array(
            'key' => 'field_6645d22e8c540',
            'label' => __('Background color', 'municipio'),
            'name' => 'background_color',
            'aria-label' => '',
            'type' => 'color_picker',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'default_value' => '',
            'enable_opacity' => 1,
            'return_format' => 'string',
        ),
    ),
    'location' => array(
        0 => array(
            0 => array(
                'param' => 'menu_item_type',
                'operator' => '==',
                'value' => 'separator',
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
    'acfe_autosync' => array(
        0 => 'json',
    ),
    'acfe_form' => 0,
    'acfe_meta' => '',
    'acfe_note' => '',
));
}