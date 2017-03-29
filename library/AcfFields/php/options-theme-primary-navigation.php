<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_56e935ea546ce',
    'title' => 'Primary navigation',
    'fields' => array(
        0 => array(
            'default_value' => 0,
            'message' => 'Enable primary navigation',
            'ui' => 0,
            'ui_on_text' => '',
            'ui_off_text' => '',
            'key' => 'field_56e938a940ac0',
            'label' => 'Enable',
            'name' => 'nav_primary_enable',
            'type' => 'true_false',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
        ),
        1 => array(
            'layout' => 'vertical',
            'choices' => array(
                'auto' => __('Automatically generated', 'municipio'),
                'wp' => __('WP Menu', 'municipio'),
            ),
            'default_value' => 'auto',
            'other_choice' => 0,
            'save_other_choice' => 0,
            'allow_null' => 0,
            'return_format' => 'value',
            'key' => 'field_56e938cc40ac1',
            'label' => __('Menu type', 'municipio'),
            'name' => 'nav_primary_type',
            'type' => 'radio',
            'instructions' => '',
            'required' => 1,
            'conditional_logic' => array(
                0 => array(
                    0 => array(
                        'field' => 'field_56e938a940ac0',
                        'operator' => '==',
                        'value' => '1',
                    ),
                ),
            ),
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
        ),
        2 => array(
            'default_value' => 0,
            'message' => __('Yes, display second menu level to active first level menu item', 'municipio'),
            'ui' => 0,
            'ui_on_text' => '',
            'ui_off_text' => '',
            'key' => 'field_56fa6428939ab',
            'label' => __('Second level', 'municipio'),
            'name' => 'nav_primariy_second_level',
            'type' => 'true_false',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => array(
                0 => array(
                    0 => array(
                        'field' => 'field_56e938a940ac0',
                        'operator' => '==',
                        'value' => '1',
                    ),
                ),
            ),
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
        ),
        3 => array(
            'message' => __('The automatically generated menu type will include all published pages from top level pages and down to the below specified depth level. The automatically generated menu is a heavy procedure for the system to run. The deeper it should go the longer loading time.', 'municipio'),
            'esc_html' => 0,
            'new_lines' => 'wpautop',
            'key' => 'field_56e93ce4914ea',
            'label' => __('Automatically generated menu', 'municipio'),
            'name' => '',
            'type' => 'message',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => array(
                0 => array(
                    0 => array(
                        'field' => 'field_56e938cc40ac1',
                        'operator' => '==',
                        'value' => 'auto',
                    ),
                    1 => array(
                        'field' => 'field_56e938a940ac0',
                        'operator' => '==',
                        'value' => '1',
                    ),
                ),
            ),
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
        ),
        4 => array(
            'multiple' => 0,
            'allow_null' => 0,
            'choices' => array(
                'all' => __('All levels', 'municipio'),
                'active' => __('Only sub levels of active top level item', 'municipio'),
            ),
            'default_value' => array(
                0 => 'all',
            ),
            'ui' => 0,
            'ajax' => 0,
            'placeholder' => '',
            'return_format' => 'value',
            'key' => 'field_56e94a6a96f90',
            'label' => __('Render', 'municipio'),
            'name' => 'nav_primary_render',
            'type' => 'select',
            'instructions' => __('How to render the items', 'municipio'),
            'required' => 1,
            'conditional_logic' => array(
                0 => array(
                    0 => array(
                        'field' => 'field_56e938a940ac0',
                        'operator' => '==',
                        'value' => '1',
                    ),
                    1 => array(
                        'field' => 'field_56e938cc40ac1',
                        'operator' => '==',
                        'value' => 'auto',
                    ),
                ),
            ),
            'wrapper' => array(
                'width' => 50,
                'class' => '',
                'id' => '',
            ),
            'disabled' => 0,
            'readonly' => 0,
        ),
        5 => array(
            'layout' => 'horizontal',
            'choices' => array(
                'left' => __('Left', 'municipio'),
                'center' => __('Center', 'municipio'),
                'right' => __('Right', 'municipio'),
                'justify' => __('Justify', 'municipio'),
            ),
            'default_value' => 'justify',
            'other_choice' => 0,
            'save_other_choice' => 0,
            'allow_null' => 0,
            'return_format' => 'value',
            'key' => 'field_56f10f0df95e3',
            'label' => __('Menu items alignment', 'municipio'),
            'name' => 'nav_primary_align',
            'type' => 'radio',
            'instructions' => '',
            'required' => 1,
            'conditional_logic' => array(
                0 => array(
                    0 => array(
                        'field' => 'field_56e938a940ac0',
                        'operator' => '==',
                        'value' => '1',
                    ),
                ),
            ),
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
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
    'menu_order' => 0,
    'position' => 'normal',
    'style' => 'default',
    'label_placement' => 'top',
    'instruction_placement' => 'label',
    'hide_on_screen' => '',
    'active' => 1,
    'description' => '',
    'local' => 'php',
));
}