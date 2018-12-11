<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_586df81d53d0f',
    'title' => 'Search results',
    'fields' => array(
        0 => array(
            'key' => 'field_586df85ba787d',
            'label' => __('Display options', 'municipio'),
            'name' => 'search_result_display_options',
            'type' => 'checkbox',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'layout' => 'horizontal',
            'choices' => array(
                'image' => __('Featured image', 'municipio'),
                'date' => __('Published date', 'municipio'),
                'lead' => __('Lead', 'municipio'),
                'url' => __('Url', 'municipio'),
            ),
            'default_value' => array(
                0 => __('date', 'municipio'),
                1 => __('lead', 'municipio'),
                2 => __('url', 'municipio'),
            ),
            'allow_custom' => 0,
            'save_custom' => 0,
            'toggle' => 0,
            'return_format' => 'value',
        ),
        1 => array(
            'key' => 'field_5885fd51fe1e4',
            'label' => __('Layout', 'municipio'),
            'name' => 'search_result_layout',
            'type' => 'radio',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'layout' => 'horizontal',
            'choices' => array(
                'default' => __('Default (list)', 'municipio'),
                'grid' => __('Grid', 'municipio'),
            ),
            'default_value' => '',
            'other_choice' => 0,
            'save_other_choice' => 0,
            'allow_null' => 0,
            'return_format' => 'value',
        ),
        2 => array(
            'key' => 'field_5885fd76fe1e5',
            'label' => __('Grid columns', 'municipio'),
            'name' => 'search_result_grid_columns',
            'type' => 'select',
            'instructions' => '',
            'required' => 1,
            'conditional_logic' => array(
                0 => array(
                    0 => array(
                        'field' => 'field_5885fd51fe1e4',
                        'operator' => '==',
                        'value' => 'grid',
                    ),
                ),
            ),
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'multiple' => 0,
            'allow_null' => 0,
            'choices' => array(
                'grid-md-12' => __('1', 'municipio'),
                'grid-md-6' => __('2', 'municipio'),
                'grid-md-4' => __('3', 'municipio'),
                'grid-md-3' => __('4', 'municipio'),
            ),
            'default_value' => array(
                0 => __('grid-md-12', 'municipio'),
            ),
            'ui' => 0,
            'ajax' => 0,
            'placeholder' => '',
            'return_format' => 'value',
        ),
    ),
    'location' => array(
        0 => array(
            0 => array(
                'param' => 'options_page',
                'operator' => '==',
                'value' => 'acf-options-search',
            ),
        ),
    ),
    'menu_order' => 0,
    'position' => 'normal',
    'style' => 'default',
    'label_placement' => 'top',
    'instruction_placement' => 'label',
    'hide_on_screen' => '',
    'active' => 1,
    'description' => '',
));
}