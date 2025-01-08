<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_677e6a05e347c',
    'title' => __('User Group Home Url', 'municipio'),
    'fields' => array(
        0 => array(
            'key' => 'field_677e6a08b2126',
            'label' => __('Select type of link', 'municipio'),
            'name' => 'user_group_type_of_link',
            'aria-label' => '',
            'type' => 'select',
            'instructions' => '',
            'required' => 1,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'choices' => array(
                'disabled' => __('Disabled', 'municipio'),
                'arbitrary_url' => __('Arbitrary URL', 'municipio'),
                'post_type' => __('Post Type', 'municipio'),
                'blog_id' => __('Network Site (multisite only)', 'municipio'),
            ),
            'default_value' => __('disabled', 'municipio'),
            'return_format' => 'value',
            'multiple' => 0,
            'allow_custom' => 0,
            'search_placeholder' => '',
            'allow_null' => 0,
            'ui' => 1,
            'ajax' => 0,
            'placeholder' => '',
        ),
        1 => array(
            'key' => 'field_677e6ad862963',
            'label' => __('Arbitrary URL', 'municipio'),
            'name' => 'arbitrary_url',
            'aria-label' => '',
            'type' => 'url',
            'instructions' => '',
            'required' => 1,
            'conditional_logic' => array(
                0 => array(
                    0 => array(
                        'field' => 'field_677e6a08b2126',
                        'operator' => '==',
                        'value' => 'arbitrary_url',
                    ),
                ),
            ),
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'default_value' => '',
            'placeholder' => '',
        ),
        2 => array(
            'key' => 'field_677e6b1765fc5',
            'label' => __('Post Type', 'municipio'),
            'name' => 'post_type',
            'aria-label' => '',
            'type' => 'acfe_post_types',
            'instructions' => '',
            'required' => 1,
            'conditional_logic' => array(
                0 => array(
                    0 => array(
                        'field' => 'field_677e6a08b2126',
                        'operator' => '==',
                        'value' => 'post_type',
                    ),
                ),
            ),
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'post_type' => '',
            'field_type' => 'select',
            'default_value' => array(
            ),
            'return_format' => 'object',
            'allow_null' => 0,
            'multiple' => 0,
            'ui' => 1,
            'ajax' => 1,
            'search_placeholder' => '',
            'allow_custom' => 0,
            'choices' => array(
            ),
            'placeholder' => '',
            'layout' => '',
            'toggle' => 0,
            'other_choice' => 0,
        ),
        3 => array(
            'key' => 'field_677e6b8534123',
            'label' => __('Blog', 'municipio'),
            'name' => 'blog_id',
            'aria-label' => '',
            'type' => 'select',
            'instructions' => '',
            'required' => 1,
            'conditional_logic' => array(
                0 => array(
                    0 => array(
                        'field' => 'field_677e6a08b2126',
                        'operator' => '==',
                        'value' => 'blog_id',
                    ),
                ),
            ),
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'choices' => array(
            ),
            'default_value' => false,
            'return_format' => 'value',
            'multiple' => 0,
            'allow_custom' => 0,
            'search_placeholder' => '',
            'allow_null' => 0,
            'ui' => 1,
            'ajax' => 1,
            'placeholder' => '',
        ),
    ),
    'location' => array(
        0 => array(
            0 => array(
                'param' => 'taxonomy',
                'operator' => '==',
                'value' => 'user_group',
            ),
        ),
    ),
    'menu_order' => 0,
    'position' => 'normal',
    'style' => 'default',
    'label_placement' => 'left',
    'instruction_placement' => 'label',
    'hide_on_screen' => '',
    'active' => true,
    'description' => '',
    'show_in_rest' => 0,
    'acfe_display_title' => '',
    'acfe_autosync' => array(
        0 => 'json',
    ),
    'acfe_form' => 0,
    'acfe_meta' => '',
    'acfe_note' => '',
));
}