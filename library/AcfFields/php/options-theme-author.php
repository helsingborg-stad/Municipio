<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_56caee123c53f',
    'title' => 'Author',
    'fields' => array(
        0 => array(
            'key' => 'field_56caee12421d3',
            'label' => __('Show author', 'municipio'),
            'name' => 'page_show_author',
            'type' => 'true_false',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => 33.3333,
                'class' => '',
                'id' => '',
            ),
            'default_value' => 1,
            'message' => __('Enable', 'municipio'),
            'ui' => 0,
            'ui_on_text' => '',
            'ui_off_text' => '',
        ),
        1 => array(
            'key' => 'field_56caeec859b73',
            'label' => __('Show author image', 'municipio'),
            'name' => 'page_show_author_image',
            'type' => 'true_false',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => 33.3333,
                'class' => '',
                'id' => '',
            ),
            'default_value' => 1,
            'message' => __('Enable', 'municipio'),
            'ui' => 0,
            'ui_on_text' => '',
            'ui_off_text' => '',
        ),
        2 => array(
            'key' => 'field_56aaeee859b74',
            'label' => __('Link name to author archive', 'municipio'),
            'name' => 'page_link_to_author_archive',
            'type' => 'true_false',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => 33.3333,
                'class' => '',
                'id' => '',
            ),
            'default_value' => 0,
            'message' => __('Enable', 'municipio'),
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
));
}