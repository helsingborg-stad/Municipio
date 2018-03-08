<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_5aa14b41551ae',
    'title' => __('2.0 Enabler', 'municipio'),
    'fields' => array(
        0 => array(
            'key' => 'field_5aa14b67f559f',
            'label' => __('Were moving to BEM(IT)', 'municipio'),
            'name' => '',
            'type' => 'message',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'message' => __('In the upcoming months we are moving to a complete BEM style of our frontend. This is eased in and hidden by default (enable checkbox below to preview). Out main focus will be: 

- Enabling the use of the customizer
- Introduce BEM in all views

Our amibition is that all functionality should be fully functional in this mode. But i\'t may break existing modifications.', 'municipio'),
            'new_lines' => 'br',
            'esc_html' => 0,
        ),
        1 => array(
            'key' => 'field_5aa14c887cb26',
            'label' => __('Activate', 'municipio'),
            'name' => 'theme_mode_2',
            'type' => 'true_false',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'message' => __('Yes, enable 2.0 preview mode', 'municipio'),
            'default_value' => 0,
            'ui' => 0,
            'ui_on_text' => '',
            'ui_off_text' => '',
        ),
    ),
    'location' => array(
        0 => array(
            0 => array(
                'param' => 'options_page',
                'operator' => '==',
                'value' => 'acf-options-theme-options',
            ),
        ),
    ),
    'menu_order' => 0,
    'position' => 'side',
    'style' => 'default',
    'label_placement' => 'top',
    'instruction_placement' => 'label',
    'hide_on_screen' => '',
    'active' => 1,
    'description' => '',
));
}