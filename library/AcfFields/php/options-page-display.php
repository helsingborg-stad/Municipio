<?php 


if (function_exists('acf_add_local_field_group')) {

    acf_add_local_field_group(array(
    'key' => 'group_56c33cf1470dc',
    'title' => 'Display settings',
    'fields' => array(
        0 => array(
            'default_value' => 0,
            'message' => __('Show featured image in single template', 'municipio'),
            'ui' => 0,
            'ui_on_text' => '',
            'ui_off_text' => '',
            'key' => 'field_56c33e148efe3',
            'label' => __('Featured image', 'municipio'),
            'name' => 'post_single_show_featured_image',
            'type' => 'true_false',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
        )
    ),
    'location' => array(
        0 => array(
            0 => array(
                'param' => 'post_type',
                'operator' => '!=',
                'value' => 'mod-notice',
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
    'local' => 'php',
));

}