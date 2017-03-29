<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_56e941cae1ed2',
    'title' => 'Sub navigation',
    'fields' => array(
        0 => array(
            'default_value' => 0,
            'message' => 'Enable sub navigation',
            'ui' => 0,
            'ui_on_text' => '',
            'ui_off_text' => '',
            'key' => 'field_56e941cae6e8b',
            'label' => 'Enable',
            'name' => 'nav_sub_enable',
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
                'sub' => __('Act as sub menu to the primary menu', 'municipio'),
                'auto' => __('Automatically generated', 'municipio'),
                'wp' => __('WP Menu', 'municipio'),
            ),
            'default_value' => 'sub',
            'other_choice' => 0,
            'save_other_choice' => 0,
            'allow_null' => 0,
            'return_format' => 'value',
            'key' => 'field_56e941cae6ea7',
            'label' => __('Menu type', 'municipio'),
            'name' => 'nav_sub_type',
            'type' => 'radio',
            'instructions' => '',
            'required' => 1,
            'conditional_logic' => array(
                0 => array(
                    0 => array(
                        'field' => 'field_56e941cae6e8b',
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
            'message' => __('The automatically generated menu type will include all published pages from top level pages and down to the below specified depth level. The automatically generated menu is a heavy procedure for the system to run. The deeper it should go the longer loading time.', 'municipio'),
            'esc_html' => 0,
            'new_lines' => 'wpautop',
            'key' => 'field_56e941cae6eb9',
            'label' => __('Automatically generated menu', 'municipio'),
            'name' => '',
            'type' => 'message',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => array(
                0 => array(
                    0 => array(
                        'field' => 'field_56e941cae6ea7',
                        'operator' => '==',
                        'value' => 'auto',
                    ),
                    1 => array(
                        'field' => 'field_56e941cae6e8b',
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
            'default_value' => 0,
            'message' => __('Include top level pages', 'municipio'),
            'ui' => 0,
            'ui_on_text' => '',
            'ui_off_text' => '',
            'key' => 'field_56e941f440e06',
            'label' => __('Top level pages', 'municipio'),
            'name' => 'nav_sub_include_top_level',
            'type' => 'true_false',
            'instructions' => __('Weather to include the top level pages. If unchecked the pages will be generated from depth 2.', 'municipio'),
            'required' => 0,
            'conditional_logic' => array(
                0 => array(
                    0 => array(
                        'field' => 'field_56e941cae6ea7',
                        'operator' => '==',
                        'value' => 'auto',
                    ),
                    1 => array(
                        'field' => 'field_56e941cae6e8b',
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
            'default_value' => 0,
            'min' => 0,
            'max' => '',
            'step' => '',
            'placeholder' => '',
            'prepend' => '',
            'append' => '',
            'key' => 'field_56e941cae6eca',
            'label' => __('Menu depth', 'municipio'),
            'name' => 'nav_sub_depth',
            'type' => 'number',
            'instructions' => __('Set to 0 to show all levels', 'municipio'),
            'required' => 1,
            'conditional_logic' => array(
                0 => array(
                    0 => array(
                        'field' => 'field_56e941cae6e8b',
                        'operator' => '==',
                        'value' => '1',
                    ),
                    1 => array(
                        'field' => 'field_56e941cae6ea7',
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
            'readonly' => 0,
            'disabled' => 0,
        ),
        5 => array(
            'multiple' => 0,
            'allow_null' => 0,
            'choices' => array(
                'all' => __('All', 'municipio'),
                'active' => __('Only active level\'s subitems', 'municipio'),
            ),
            'default_value' => array(
                0 => 0,
            ),
            'ui' => 0,
            'ajax' => 0,
            'placeholder' => '',
            'return_format' => 'value',
            'key' => 'field_56e94b90e1eed',
            'label' => __('Render', 'municipio'),
            'name' => 'nav_sub_render',
            'type' => 'select',
            'instructions' => __('How to render the items', 'municipio'),
            'required' => 1,
            'conditional_logic' => array(
                0 => array(
                    0 => array(
                        'field' => 'field_56e941cae6e8b',
                        'operator' => '==',
                        'value' => '1',
                    ),
                    1 => array(
                        'field' => 'field_56e941cae6ea7',
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