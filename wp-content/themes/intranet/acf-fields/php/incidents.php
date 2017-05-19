<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_57bade5cb86d5',
    'title' => __('Incident', 'municipio-intranet'),
    'fields' => array(
        0 => array(
            'layout' => 'horizontal',
            'choices' => array(
                'info' => __('Low', 'municipio-intranet'),
                'warning' => __('Medium', 'municipio-intranet'),
                'danger' => __('High', 'municipio-intranet'),
            ),
            'default_value' => 1,
            'other_choice' => 0,
            'save_other_choice' => 0,
            'allow_null' => 0,
            'return_format' => 'value',
            'key' => 'field_57badebe20873',
            'label' => __('User impact level', 'municipio-intranet'),
            'name' => 'level',
            'type' => 'radio',
            'instructions' => '',
            'required' => 1,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
        ),
        1 => array(
            'display_format' => 'Y-m-d H:i:s',
            'return_format' => 'Y-m-d H:i:s',
            'first_day' => 1,
            'key' => 'field_57bade6320871',
            'label' => __('Start date', 'municipio-intranet'),
            'name' => 'start_date',
            'type' => 'date_time_picker',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '50',
                'class' => '',
                'id' => '',
            ),
        ),
        2 => array(
            'display_format' => 'Y-m-d H:i:s',
            'return_format' => 'Y-m-d H:i:s',
            'first_day' => 1,
            'key' => 'field_57bade9f20872',
            'label' => __('End date', 'municipio-intranet'),
            'name' => 'end_date',
            'type' => 'date_time_picker',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '50',
                'class' => '',
                'id' => '',
            ),
        ),
    ),
    'location' => array(
        0 => array(
            0 => array(
                'param' => 'post_type',
                'operator' => '==',
                'value' => 'incidents',
            ),
        ),
    ),
    'menu_order' => 0,
    'position' => 'acf_after_title',
    'style' => 'default',
    'label_placement' => 'top',
    'instruction_placement' => 'label',
    'hide_on_screen' => '',
    'active' => 1,
    'description' => '',
));
}