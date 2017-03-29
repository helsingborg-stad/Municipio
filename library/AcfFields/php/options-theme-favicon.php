<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_56cc39aba8782',
    'title' => 'Favicon',
    'fields' => array(
        0 => array(
            'sub_fields' => array(
                0 => array(
                    'multiple' => 0,
                    'allow_null' => 0,
                    'choices' => array(
                        'fav' => 'favicon.ico (16x16px, 32x32px, 48x48px)',
                        152 => 'iOS, Android (152x152px)',
                        144 => 'IE10, Windows Metro (144x144px)',
                    ),
                    'default_value' => array(
                    ),
                    'ui' => 0,
                    'ajax' => 0,
                    'placeholder' => '',
                    'return_format' => 'value',
                    'key' => 'field_56cc3f70c0b42',
                    'label' => __('Type', 'municipio'),
                    'name' => 'favicon_type',
                    'type' => 'select',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'disabled' => 0,
                    'readonly' => 0,
                ),
                1 => array(
                    'return_format' => 'array',
                    'preview_size' => 'full',
                    'library' => 'all',
                    'min_width' => 152,
                    'min_height' => 152,
                    'min_size' => '',
                    'max_width' => 152,
                    'max_height' => 152,
                    'max_size' => '',
                    'mime_types' => '',
                    'key' => 'field_56cc3f8685425',
                    'label' => __('Icon 152x152', 'municipio'),
                    'name' => 'favicon_icon',
                    'type' => 'image',
                    'instructions' => __('Must be 152x152 pixels .png', 'municipio'),
                    'required' => 1,
                    'conditional_logic' => array(
                        0 => array(
                            0 => array(
                                'field' => 'field_56cc3f70c0b42',
                                'operator' => '==',
                                'value' => '152',
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
                    'return_format' => 'array',
                    'preview_size' => 'full',
                    'library' => 'all',
                    'min_width' => 144,
                    'min_height' => 144,
                    'min_size' => '',
                    'max_width' => 144,
                    'max_height' => 144,
                    'max_size' => '',
                    'mime_types' => 'png',
                    'key' => 'field_56cc3fc585426',
                    'label' => __('Icon 144x144', 'municipio'),
                    'name' => 'favicon_icon',
                    'type' => 'image',
                    'instructions' => __('Must be 144x144 pixels .png', 'municipio'),
                    'required' => 1,
                    'conditional_logic' => array(
                        0 => array(
                            0 => array(
                                'field' => 'field_56cc3f70c0b42',
                                'operator' => '==',
                                'value' => '144',
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
                    'return_format' => 'array',
                    'library' => 'all',
                    'min_size' => '',
                    'max_size' => '',
                    'mime_types' => '.ico',
                    'key' => 'field_56cc400885427',
                    'label' => __('Favicon.ico', 'municipio'),
                    'name' => 'favicon_icon',
                    'type' => 'file',
                    'instructions' => __('Must be a .ico containing 16x16px, 32x32px and 48x48px', 'municipio'),
                    'required' => 1,
                    'conditional_logic' => array(
                        0 => array(
                            0 => array(
                                'field' => 'field_56cc3f70c0b42',
                                'operator' => '==',
                                'value' => 'fav',
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
                    'return_format' => 'array',
                    'preview_size' => 'thumbnail',
                    'library' => 'all',
                    'min_width' => '',
                    'min_height' => '',
                    'min_size' => '',
                    'max_width' => '',
                    'max_height' => '',
                    'max_size' => '',
                    'mime_types' => '',
                    'key' => 'field_56cc404d85428',
                    'label' => __('Icon', 'municipio'),
                    'name' => 'favicon_icon',
                    'type' => 'image',
                    'instructions' => '',
                    'required' => 1,
                    'conditional_logic' => array(
                        0 => array(
                            0 => array(
                                'field' => 'field_56cc3f70c0b42',
                                'operator' => '!=',
                                'value' => 'fav',
                            ),
                            1 => array(
                                'field' => 'field_56cc3f70c0b42',
                                'operator' => '!=',
                                'value' => '152',
                            ),
                            2 => array(
                                'field' => 'field_56cc3f70c0b42',
                                'operator' => '!=',
                                'value' => '144',
                            ),
                        ),
                    ),
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                ),
                5 => array(
                    'default_value' => '#FFFFFF',
                    'key' => 'field_56cc5290155fc',
                    'label' => __('Metro tile color', 'municipio'),
                    'name' => 'favicon_tile_color',
                    'type' => 'color_picker',
                    'instructions' => '',
                    'required' => 1,
                    'conditional_logic' => array(
                        0 => array(
                            0 => array(
                                'field' => 'field_56cc3f70c0b42',
                                'operator' => '==',
                                'value' => '144',
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
            'min' => 0,
            'max' => 0,
            'layout' => 'block',
            'button_label' => __('Lägg till rad', 'municipio'),
            'collapsed' => '',
            'key' => 'field_56cc3f64c0b41',
            'label' => __('Favicons', 'municipio'),
            'name' => 'favicons',
            'type' => 'repeater',
            'instructions' => __('Add favicons in various sizes. If a size has multiple icons specified only the latest specified icon will be used.', 'municipio'),
            'required' => 0,
            'conditional_logic' => 0,
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
                'value' => 'acf-options-theme-options',
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