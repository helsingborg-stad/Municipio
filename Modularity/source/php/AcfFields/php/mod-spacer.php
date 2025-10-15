<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_611cffa40276a',
    'title' => __('Whitespace', 'modularity'),
    'fields' => array(
        0 => array(
            'key' => 'field_611d0016546f1',
            'label' => __('Amount of whitespace', 'modularity'),
            'name' => 'space_amount',
            'type' => 'range',
            'instructions' => __('All space will be multiplied by 8.', 'modularity'),
            'required' => 1,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'default_value' => 4,
            'min' => '',
            'max' => 24,
            'step' => 2,
            'prepend' => '',
            'append' => '',
        ),
    ),
    'location' => array(
        0 => array(
            0 => array(
                'param' => 'post_type',
                'operator' => '==',
                'value' => 'mod-spacer',
            ),
        ),
        1 => array(
            0 => array(
                'param' => 'block',
                'operator' => '==',
                'value' => 'acf/spacer',
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