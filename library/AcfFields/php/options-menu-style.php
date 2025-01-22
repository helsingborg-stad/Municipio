<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_61dc486660615',
    'title' => __('Navigation Item Style', 'municipio'),
    'fields' => array(
        0 => array(
            'key' => 'field_61dc486ea78df',
            'label' => __('Style', 'municipio'),
            'name' => 'menu_item_style',
            'aria-label' => '',
            'type' => 'select',
            'instructions' => __('Display as button should be limited to one item per menu. This item should not have any children.', 'municipio'),
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'choices' => array(
                'default' => __('Default', 'municipio'),
                'button' => __('Button', 'municipio'),
            ),
            'default_value' => 'default',
            'allow_null' => 0,
            'multiple' => 0,
            'ui' => 0,
            'return_format' => 'value',
            'ajax' => 0,
            'placeholder' => '',
            'allow_custom' => 0,
            'search_placeholder' => '',
        ),
    ),
    'location' => array(
        0 => array(
            0 => array(
                'param' => 'nav_menu_item',
                'operator' => '==',
                'value' => 'location/main-menu',
            ),
        ),
        1 => array(
            0 => array(
                'param' => 'nav_menu_item',
                'operator' => '==',
                'value' => 'location/mega-menu',
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
    'show_in_rest' => 0,
    'acfe_display_title' => '',
    'acfe_autosync' => '',
    'acfe_form' => 0,
    'acfe_meta' => '',
    'acfe_note' => '',
));
}