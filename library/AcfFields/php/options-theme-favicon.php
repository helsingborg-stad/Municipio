<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_56cc39aba8782',
    'title' => 'Favicon',
    'fields' => array(
        0 => array(
            'key' => 'field_56cc3f64c0b41',
            'label' => __('Favoritikoner', 'municipio'),
            'name' => 'favicons',
            'type' => 'repeater',
            'instructions' => __('Lägg till favoritikoner i olika storlekar. Om en storlek har multipla värden kommer bara den senast specificerade att användas.', 'municipio'),
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'min' => 0,
            'max' => 0,
            'layout' => 'block',
            'button_label' => __('Lägg till rad', 'municipio'),
            'collapsed' => '',
            'sub_fields' => array(
                0 => array(
                    'key' => 'field_56cc3f70c0b42',
                    'label' => __('Typ', 'municipio'),
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
                    'multiple' => 0,
                    'allow_null' => 0,
                    'choices' => array(
                        'fav' => 'favicon.ico (16x16px, 32x32px, 48x48px)',
                        152 => 'iOS, Android (152x152px)',
                        144 => 'IE10, Windows Metro (144x144px)',
                    ),
                    'default_value' => false,
                    'ui' => 0,
                    'ajax' => 0,
                    'placeholder' => '',
                    'return_format' => 'value',
                    'disabled' => 0,
                    'readonly' => 0,
                ),
                1 => array(
                    'key' => 'field_56cc3f8685425',
                    'label' => __('Ikon 152x152', 'municipio'),
                    'name' => 'favicon_icon',
                    'type' => 'image',
                    'instructions' => __('Måste vara 152x152 pixlar och format .png', 'municipio'),
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
                ),
                2 => array(
                    'key' => 'field_56cc3fc585426',
                    'label' => __('Ikon 144x144', 'municipio'),
                    'name' => 'favicon_icon',
                    'type' => 'image',
                    'instructions' => __('Måste vara 144x144 pixlar och format .png', 'municipio'),
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
                ),
                3 => array(
                    'key' => 'field_56cc400885427',
                    'label' => __('Favicon.ico', 'municipio'),
                    'name' => 'favicon_icon',
                    'type' => 'file',
                    'instructions' => __('Måste vara en .ico innehållandes 16x16px, 32x32px and 48x48px', 'municipio'),
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
                    'return_format' => 'array',
                    'library' => 'all',
                    'min_size' => '',
                    'max_size' => '',
                    'mime_types' => '.ico',
                ),
                4 => array(
                    'key' => 'field_56cc404d85428',
                    'label' => __('Ikon', 'municipio'),
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
                ),
                5 => array(
                    'key' => 'field_56cc5290155fc',
                    'label' => __('Bakgrundsfärg på Metro-bricka', 'municipio'),
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
                    'default_value' => '#FFFFFF',
                ),
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
    'menu_order' => 8,
    'position' => 'normal',
    'style' => 'default',
    'label_placement' => 'top',
    'instruction_placement' => 'label',
    'hide_on_screen' => '',
    'active' => true,
    'description' => '',
));
}