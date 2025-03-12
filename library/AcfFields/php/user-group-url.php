<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_677e6a05e347c',
    'title' => __('User Group Home Url', 'municipio'),
    'fields' => array(
        0 => array(
            'key' => 'field_67d1aa4aedab2',
            'label' => __('User Group URL', 'municipio'),
            'name' => '',
            'aria-label' => '',
            'type' => 'message',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'is_publicly_hidden' => 0,
            'is_privately_hidden' => 0,
            'message' => __('This feature adds the following functionality: 
- Display a link to the usergroups homepage as a toast if any defined. 
- An optional redirect will be offered to the user, if they want to be redirected to the homepage automatically after login.', 'municipio'),
            'new_lines' => 'wpautop',
            'esc_html' => 0,
        ),
        1 => array(
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
        2 => array(
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
        3 => array(
            'key' => 'field_677e6b1765fc5',
            'label' => __('Post Type', 'municipio'),
            'name' => 'post_type',
            'aria-label' => '',
            'type' => 'posttype_select',
            'instructions' => '',
            'required' => 0,
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
            'default_value' => '',
            'allow_null' => 0,
            'multiple' => 0,
            'placeholder' => '',
            'disabled' => 0,
            'readonly' => 0,
        ),
        4 => array(
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
                1 => __('https://dev.local.municipio.tech/', 'municipio'),
                2 => __('https://dev.local.municipio.tech/dev-one/', 'municipio'),
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
        5 => array(
            'key' => 'field_67d1a95c1a77f',
            'label' => __('User can prefer group url', 'municipio'),
            'name' => 'user_group_user_can_prefer_group_url',
            'aria-label' => '',
            'type' => 'true_false',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => array(
                0 => array(
                    0 => array(
                        'field' => 'field_677e6a08b2126',
                        'operator' => '!=',
                        'value' => 'disabled',
                    ),
                ),
            ),
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'is_publicly_hidden' => 0,
            'is_privately_hidden' => 0,
            'message' => __('This option enables the ability for the user to save the user group page as homepage (after login).', 'municipio'),
            'default_value' => 1,
            'ui_on_text' => __('Enabled', 'municipio'),
            'ui_off_text' => __('Disabled', 'municipio'),
            'ui' => 1,
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
    'label_placement' => 'top',
    'instruction_placement' => 'label',
    'hide_on_screen' => '',
    'active' => true,
    'description' => '',
    'show_in_rest' => 0,
));
}