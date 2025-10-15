<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_571dffc63090c',
    'title' => __('Data sorting', 'modularity'),
    'fields' => array(
        0 => array(
            'key' => 'field_571dffca1d90b',
            'label' => __('Sort by', 'modularity'),
            'name' => 'posts_sort_by',
            'aria-label' => '',
            'type' => 'select',
            'instructions' => '',
            'required' => 1,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => 50,
                'class' => '',
                'id' => 'modularity-sorted-by',
            ),
            'multiple' => 0,
            'allow_null' => 0,
            'choices' => array(
                'false' => __('Do not sort', 'modularity'),
                'title' => __('Titel', 'modularity'),
                'date' => __('Publiceringsdatum', 'modularity'),
                'modified' => __('Date modified', 'modularity'),
                'rand' => __('Random', 'modularity'),
                'menu_order' => __('Menu order', 'modularity'),
            ),
            'default_value' => 'date',
            'ui' => 0,
            'ajax' => 0,
            'placeholder' => '',
            'return_format' => 'value',
            'disabled' => 0,
            'readonly' => 0,
            'allow_custom' => 0,
            'search_placeholder' => '',
        ),
        1 => array(
            'key' => 'field_571e00241d90c',
            'label' => __('Order', 'modularity'),
            'name' => 'posts_sort_order',
            'aria-label' => '',
            'type' => 'radio',
            'instructions' => '',
            'required' => 1,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => 50,
                'class' => '',
                'id' => '',
            ),
            'layout' => 'horizontal',
            'choices' => array(
                'asc' => __('Ascending', 'modularity'),
                'desc' => __('Descending', 'modularity'),
            ),
            'default_value' => '',
            'other_choice' => 0,
            'save_other_choice' => 0,
            'allow_null' => 0,
            'return_format' => 'value',
        ),
    ),
    'location' => array(
        0 => array(
            0 => array(
                'param' => 'post_type',
                'operator' => '==',
                'value' => 'mod-posts',
            ),
        ),
        1 => array(
            0 => array(
                'param' => 'block',
                'operator' => '==',
                'value' => 'acf/posts',
            ),
        ),
    ),
    'menu_order' => 10,
    'position' => 'normal',
    'style' => 'default',
    'label_placement' => 'top',
    'instruction_placement' => 'label',
    'hide_on_screen' => '',
    'active' => true,
    'description' => '',
    'show_in_rest' => false,
    'modified' => 1461661083,
));
}