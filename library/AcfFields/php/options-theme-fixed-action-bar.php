<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_5a2957d095e95',
    'title' => __('Fixed action bar (FAB)', 'municipio'),
    'fields' => array(
        0 => array(
            'key' => 'field_5a2957f44818b',
            'label' => __('FAB settings', 'municipio'),
            'name' => 'fab_settings',
            'type' => 'radio',
            'value' => NULL,
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'choices' => array(
                'disabled' => __('Disabled', 'municipio'),
                'wp' => __('WP Menu', 'municipio'),
            ),
            'allow_null' => 0,
            'other_choice' => 0,
            'save_other_choice' => 0,
            'default_value' => '',
            'layout' => 'horizontal',
            'return_format' => 'value',
        ),
        1 => array(
            'key' => 'field_5a29586d4818c',
            'label' => __('WP Menu', 'municipio'),
            'name' => 'fab_wp_menu',
            'type' => 'select',
            'value' => NULL,
            'instructions' => '',
            'required' => 1,
            'conditional_logic' => array(
                0 => array(
                    0 => array(
                        'field' => 'field_5a2957f44818b',
                        'operator' => '==',
                        'value' => 'wp',
                    ),
                ),
            ),
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'choices' => array(
                2 => __('huvudmeny', 'municipio'),
                14 => __('Länkar', 'municipio'),
            ),
            'default_value' => array(
            ),
            'allow_null' => 0,
            'multiple' => 0,
            'ui' => 0,
            'ajax' => 0,
            'return_format' => 'value',
            'placeholder' => '',
        ),
        2 => array(
            'key' => 'field_5a295c4ba850a',
            'label' => __('Visabllity', 'municipio'),
            'name' => 'fab_visabllity',
            'type' => 'checkbox',
            'value' => NULL,
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => array(
                0 => array(
                    0 => array(
                        'field' => 'field_5a2957f44818b',
                        'operator' => '!=',
                        'value' => 'disabled',
                    ),
                ),
            ),
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'choices' => array(
                'hidden-xs' => __('Hide on extra small devices', 'municipio'),
                'hidden-sm' => __('Hide on small devices', 'municipio'),
                'hidden-md' => __('Hide on medium devices', 'municipio'),
                'hidden-lg' => __('Hide on large devices', 'municipio'),
            ),
            'allow_custom' => 0,
            'save_custom' => 0,
            'default_value' => array(
            ),
            'layout' => 'vertical',
            'toggle' => 0,
            'return_format' => 'value',
        ),
    ),
    'location' => array(
        0 => array(
            0 => array(
                'param' => 'options_page',
                'operator' => '==',
                'value' => 'acf-options-navigation',
            ),
        ),
    ),
    'menu_order' => 20,
    'position' => 'normal',
    'style' => 'default',
    'label_placement' => 'top',
    'instruction_placement' => 'label',
    'hide_on_screen' => '',
    'active' => 1,
    'description' => '',
));
}