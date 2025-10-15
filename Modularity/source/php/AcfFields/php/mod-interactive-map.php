<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_67a6218f4b8a6',
    'title' => __('Interactive Map', 'modularity'),
    'fields' => array(
        0 => array(
            'key' => 'field_67dd6e61f1d7d',
            'label' => __('Size', 'modularity'),
            'name' => 'mod_interactive_map_size',
            'aria-label' => '',
            'type' => 'select',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'choices' => array(
                'small' => __('Small', 'modularity'),
                'medium' => __('Medium', 'modularity'),
                'large' => __('Large', 'modularity'),
            ),
            'default_value' => __('medium', 'modularity'),
            'return_format' => 'value',
            'multiple' => 0,
            'allow_null' => 0,
            'allow_in_bindings' => 0,
            'ui' => 0,
            'ajax' => 0,
            'placeholder' => '',
            'allow_custom' => 0,
            'search_placeholder' => '',
        ),
        1 => array(
            'key' => 'field_67b44cbc181c6',
            'label' => __('Interactive Map', 'modularity'),
            'name' => 'interactive-map',
            'aria-label' => '',
            'type' => 'openstreetmap',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'default_lat' => '',
            'default_lng' => '',
            'allow_in_bindings' => 1,
        ),
    ),
    'location' => array(
        0 => array(
            0 => array(
                'param' => 'post_type',
                'operator' => '==',
                'value' => 'mod-interactivemap',
            ),
        ),
        1 => array(
            0 => array(
                'param' => 'block',
                'operator' => '==',
                'value' => 'acf/interactivemap',
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