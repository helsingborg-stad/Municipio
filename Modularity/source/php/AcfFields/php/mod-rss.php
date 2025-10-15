<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_59535d940706c',
    'title' => 'RSS',
    'fields' => array(
        0 => array(
            'key' => 'field_59535db89b29b',
            'label' => __('RSS feed URL', 'modularity'),
            'name' => 'rss_url',
            'type' => 'url',
            'instructions' => '',
            'required' => 1,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'default_value' => '',
            'placeholder' => '',
        ),
        1 => array(
            'key' => 'field_59535e3782765',
            'label' => __('Number of items', 'modularity'),
            'name' => 'items',
            'type' => 'number',
            'instructions' => __('Set to -1 to show all', 'modularity'),
            'required' => 1,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '50',
                'class' => '',
                'id' => '',
            ),
            'default_value' => '',
            'placeholder' => '',
            'prepend' => '',
            'append' => '',
            'min' => '',
            'max' => '',
            'step' => '',
        ),
        2 => array(
            'key' => 'field_595360d0232cc',
            'label' => __('Sort order', 'modularity'),
            'name' => 'sort_order',
            'type' => 'radio',
            'instructions' => __('Select order to sort the RSS feed', 'modularity'),
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '50',
                'class' => '',
                'id' => '',
            ),
            'choices' => array(
                'desc' => __('Descending', 'modularity'),
                'asc' => __('Ascending', 'modularity'),
            ),
            'allow_null' => 0,
            'other_choice' => 0,
            'save_other_choice' => 0,
            'default_value' => '',
            'layout' => 'vertical',
            'return_format' => 'value',
        ),
        3 => array(
            'key' => 'field_59535e9382766',
            'label' => __('Fields', 'modularity'),
            'name' => 'fields',
            'type' => 'checkbox',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'choices' => array(
                'summary' => __('Summary', 'modularity'),
                'author' => __('Author', 'modularity'),
                'date' => __('Date', 'modularity'),
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
                'param' => 'post_type',
                'operator' => '==',
                'value' => 'mod-rss',
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