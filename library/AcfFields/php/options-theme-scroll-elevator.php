<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_5825be470579f',
    'title' => 'Scroll elevator',
    'fields' => array(
        0 => array(
            'message' => __('Adds a button that scrolls the user to the very top of the page. Enables on the class "scroll-elevator-toggle". When the window scroll position is a the bottom of the "scroll-elevator-toggle" element the button will be displayed.', 'municipio'),
            'esc_html' => 0,
            'new_lines' => 'wpautop',
            'key' => 'field_5825be4ee64a1',
            'label' => '',
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
        ),
        1 => array(
            'default_value' => 0,
            'message' => __('Enable scroll elevator', 'municipio'),
            'ui' => 0,
            'ui_on_text' => '',
            'ui_off_text' => '',
            'key' => 'field_56e938a940ac1',
            'label' => __('Enable', 'municipio'),
            'name' => 'scroll_elevator_enabled',
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
        2 => array(
            'default_value' => '',
            'maxlength' => '',
            'placeholder' => '',
            'prepend' => '',
            'append' => '',
            'key' => 'field_5825bf01e64a4',
            'label' => __('Call to action text', 'municipio'),
            'name' => 'scroll_elevator_text',
            'type' => 'text',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => array(
                0 => array(
                    0 => array(
                        'field' => 'field_56e938a940ac1',
                        'operator' => '==',
                        'value' => '1',
                    ),
                ),
            ),
            'wrapper' => array(
                'width' => '33.3333',
                'class' => '',
                'id' => '',
            ),
        ),
        3 => array(
            'default_value' => '',
            'maxlength' => '',
            'placeholder' => '',
            'prepend' => '',
            'append' => '',
            'key' => 'field_5825be64e64a2',
            'label' => __('Tooltip', 'municipio'),
            'name' => 'scroll_elevator_tooltio',
            'type' => 'text',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => array(
                0 => array(
                    0 => array(
                        'field' => 'field_56e938a940ac1',
                        'operator' => '==',
                        'value' => '1',
                    ),
                ),
            ),
            'wrapper' => array(
                'width' => '33.3333',
                'class' => '',
                'id' => '',
            ),
        ),
        4 => array(
            'multiple' => 0,
            'allow_null' => 0,
            'choices' => array(
                'data-tooltip-left' => __('Left', 'municipio'),
                'data-tooltip-right' => __('Right', 'municipio'),
                'data-tooltip-top' => __('Top', 'municipio'),
                'data-tooltip-bottom' => __('Bottom', 'municipio'),
            ),
            'default_value' => array(
            ),
            'ui' => 0,
            'ajax' => 0,
            'placeholder' => '',
            'return_format' => 'value',
            'key' => 'field_5825bebee64a3',
            'label' => __('Tooltip position', 'municipio'),
            'name' => 'scroll_elevator_tooltio_position',
            'type' => 'select',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => array(
                0 => array(
                    0 => array(
                        'field' => 'field_56e938a940ac1',
                        'operator' => '==',
                        'value' => '1',
                    ),
                ),
            ),
            'wrapper' => array(
                'width' => '33.3333',
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
                'value' => 'acf-options-content',
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