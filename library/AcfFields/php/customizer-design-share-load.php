<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_604a117fe6872',
    'title' => __('Avabile Designs', 'municipio'),
    'fields' => array(
        0 => array(
            'key' => 'field_604a1189b3b40',
            'label' => __('Select design', 'municipio'),
            'name' => 'customizer_select_designshare',
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
                '2640cec0ee92c786aba21dc24ff79091' => __('Helsingborg.se (https://developement.local.test )', 'municipio'),
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
                'value' => 'loaddesign',
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