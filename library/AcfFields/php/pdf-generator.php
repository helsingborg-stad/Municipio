<?php 


if (function_exists('acf_add_local_field_group')) {

    acf_add_local_field_group(array(
    'key' => 'group_65538baa43fb2',
    'title' => __('Pdf Generator', 'municipio'),
    'fields' => array(
        0 => array(
            'key' => 'field_655633c401312',
            'label' => __('Default frontpage', 'municipio'),
            'name' => 'default_frontpage',
            'type' => 'radio',
            'instructions' => __('If there is no data attached. What template should it use?', 'municipio'),
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'choices' => array(
                'default' => __('Default', 'municipio'),
                'none' => __('None', 'municipio'),
                'custom' => __('Custom', 'municipio'),
            ),
            'default_value' => __('default', 'municipio'),
            'return_format' => 'value',
            'allow_null' => 0,
            'other_choice' => 0,
            'layout' => 'vertical',
            'save_other_choice' => 0,
        ),
        1 => array(
            'key' => 'field_6556381b6eefe',
            'label' => __('Chose another frontpage', 'municipio'),
            'name' => 'chose_another_frontpage',
            'type' => 'select',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => array(
                0 => array(
                    0 => array(
                        'field' => 'field_655633c401312',
                        'operator' => '==',
                        'value' => 'custom',
                    ),
                ),
            ),
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'choices' => array(
                1 => __('1', 'municipio'),
                2 => __('2', 'municipio'),
                3 => __('3', 'municipio'),
                4 => __('4', 'municipio'),
            ),
            'default_value' => 1,
            'return_format' => 'value',
            'multiple' => 0,
            'allow_null' => 0,
            'ui' => 0,
            'ajax' => 0,
            'placeholder' => '',
        ),
    ),
    'location' => array(
        0 => array(
            0 => array(
                'param' => 'options_page',
                'operator' => '==',
                'value' => 'acf-options-css',
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