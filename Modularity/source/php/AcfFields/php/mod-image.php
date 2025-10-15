<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_570770ab8f064',
    'title' => __('Image', 'modularity'),
    'fields' => array(
        0 => array(
            'key' => 'field_570770b8e2e61',
            'label' => __('Image', 'modularity'),
            'name' => 'mod_image_image',
            'aria-label' => '',
            'type' => 'image',
            'instructions' => __('Allowed file types: jpg, png, gif', 'modularity'),
            'required' => 1,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'uploader' => '',
            'return_format' => 'id',
            'acfe_thumbnail' => 0,
            'min_width' => '',
            'min_height' => '',
            'min_size' => '',
            'max_width' => '',
            'max_height' => '',
            'max_size' => '',
            'mime_types' => 'jpg, png, gif',
            'preview_size' => 'medium',
            'library' => 'all',
        ),
        1 => array(
            'key' => 'field_587604df2975f',
            'label' => __('Image caption', 'modularity'),
            'name' => 'mod_image_caption',
            'aria-label' => '',
            'type' => 'textarea',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'default_value' => '',
            'new_lines' => 'br',
            'maxlength' => '',
            'placeholder' => '',
            'rows' => 4,
            'acfe_textarea_code' => 0,
        ),
        2 => array(
            'key' => 'field_577d07c8d72db',
            'label' => __('Link', 'modularity'),
            'name' => 'mod_image_link',
            'aria-label' => '',
            'type' => 'radio',
            'instructions' => '',
            'required' => 1,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'layout' => 'horizontal',
            'choices' => array(
                'false' => __('None', 'modularity'),
                'internal' => __('Internal', 'modularity'),
                'external' => __('External', 'modularity'),
            ),
            'default_value' => '',
            'other_choice' => 0,
            'save_other_choice' => 0,
            'allow_null' => 0,
            'return_format' => 'value',
        ),
        3 => array(
            'key' => 'field_577d0810d72dc',
            'label' => __('Link url', 'modularity'),
            'name' => 'mod_image_link_url',
            'aria-label' => '',
            'type' => 'url',
            'instructions' => '',
            'required' => 1,
            'conditional_logic' => array(
                0 => array(
                    0 => array(
                        'field' => 'field_577d07c8d72db',
                        'operator' => '==',
                        'value' => 'external',
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
        4 => array(
            'key' => 'field_577d0840d72dd',
            'label' => __('Link page', 'modularity'),
            'name' => 'mod_image_link_url',
            'aria-label' => '',
            'type' => 'page_link',
            'instructions' => '',
            'required' => 1,
            'conditional_logic' => array(
                0 => array(
                    0 => array(
                        'field' => 'field_577d07c8d72db',
                        'operator' => '==',
                        'value' => 'internal',
                    ),
                ),
            ),
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'post_type' => array(
            ),
            'taxonomy' => array(
            ),
            'allow_null' => 0,
            'multiple' => 0,
            'allow_archives' => 1,
        ),
    ),
    'location' => array(
        0 => array(
            0 => array(
                'param' => 'post_type',
                'operator' => '==',
                'value' => 'mod-image',
            ),
        ),
        1 => array(
            0 => array(
                'param' => 'block',
                'operator' => '==',
                'value' => 'acf/image',
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
    'acfe_display_title' => '',
    'acfe_autosync' => '',
    'acfe_form' => 0,
    'acfe_meta' => '',
    'acfe_note' => '',
));
}