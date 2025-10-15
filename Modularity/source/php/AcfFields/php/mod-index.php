<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_569ceab2c16ee',
    'title' => __('Index', 'modularity'),
    'fields' => array(
        0 => array(
            'key' => 'field_569ceabc2cfc8',
            'label' => __('Index', 'modularity'),
            'name' => 'index',
            'type' => 'repeater',
            'instructions' => '',
            'required' => 1,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'min' => 1,
            'max' => 0,
            'layout' => 'block',
            'button_label' => __('Lägg till rad', 'modularity'),
            'collapsed' => '',
            'sub_fields' => array(
                0 => array(
                    'key' => 'field_5743f66719b62',
                    'label' => __('Link type', 'modularity'),
                    'name' => 'link_type',
                    'type' => 'radio',
                    'instructions' => '',
                    'required' => 1,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'choices' => array(
                        'internal' => __('Internal', 'modularity'),
                        'external' => __('External', 'modularity'),
                        'unlinked' => __('No Link', 'modularity'),
                    ),
                    'allow_null' => 0,
                    'other_choice' => 0,
                    'default_value' => '',
                    'layout' => 'horizontal',
                    'return_format' => 'value',
                    'save_other_choice' => 0,
                ),
                1 => array(
                    'key' => 'field_569cf1252cfc9',
                    'label' => __('Page', 'modularity'),
                    'name' => 'page',
                    'type' => 'post_object',
                    'instructions' => '',
                    'required' => 1,
                    'conditional_logic' => array(
                        0 => array(
                            0 => array(
                                'field' => 'field_5743f66719b62',
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
                    'return_format' => 'object',
                    'ui' => 1,
                ),
                2 => array(
                    'key' => 'field_5743f6be19b63',
                    'label' => __('Link url', 'modularity'),
                    'name' => 'link_url',
                    'type' => 'url',
                    'instructions' => '',
                    'required' => 1,
                    'conditional_logic' => array(
                        0 => array(
                            0 => array(
                                'field' => 'field_5743f66719b62',
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
                3 => array(
                    'key' => 'field_569cf1762cfca',
                    'label' => __('Image', 'modularity'),
                    'name' => 'image_display',
                    'type' => 'radio',
                    'instructions' => '',
                    'required' => 1,
                    'conditional_logic' => array(
                        0 => array(
                            0 => array(
                                'field' => 'field_5743f66719b62',
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
                    'choices' => array(
                        'featured' => __('Use featured image', 'modularity'),
                        'custom' => __('Upload custom image', 'modularity'),
                        'false' => __('Hide image', 'modularity'),
                    ),
                    'allow_null' => 0,
                    'other_choice' => 0,
                    'default_value' => __('featured', 'modularity'),
                    'layout' => 'vertical',
                    'return_format' => 'value',
                    'save_other_choice' => 0,
                ),
                4 => array(
                    'key' => 'field_569e08cfb642a',
                    'label' => __('Upload image', 'modularity'),
                    'name' => 'custom_image',
                    'type' => 'image',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => array(
                        0 => array(
                            0 => array(
                                'field' => 'field_569cf1762cfca',
                                'operator' => '==',
                                'value' => 'custom',
                            ),
                        ),
                        1 => array(
                            0 => array(
                                'field' => 'field_5743f66719b62',
                                'operator' => '==',
                                'value' => 'external',
                            ),
                        ),
                        2 => array(
                            0 => array(
                                'field' => 'field_5743f66719b62',
                                'operator' => '==',
                                'value' => 'unlinked',
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
                    'key' => 'field_56c5e0edc81bb',
                    'label' => __('Titel', 'modularity'),
                    'name' => 'title',
                    'type' => 'text',
                    'instructions' => __('Om du inte vill använda den underliggande sidans titel kan du skriva in en annan titel här.', 'modularity'),
                    'required' => 0,
                    'conditional_logic' => array(
                        0 => array(
                            0 => array(
                                'field' => 'field_5743f66719b62',
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
                    'default_value' => '',
                    'placeholder' => '',
                    'prepend' => '',
                    'append' => '',
                    'maxlength' => '',
                ),
                6 => array(
                    'key' => 'field_5743f6da19b64',
                    'label' => __('Titel', 'modularity'),
                    'name' => 'title',
                    'type' => 'text',
                    'instructions' => '',
                    'required' => 1,
                    'conditional_logic' => array(
                        0 => array(
                            0 => array(
                                'field' => 'field_5743f66719b62',
                                'operator' => '==',
                                'value' => 'external',
                            ),
                        ),
                        1 => array(
                            0 => array(
                                'field' => 'field_5743f66719b62',
                                'operator' => '==',
                                'value' => 'unlinked',
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
                    'prepend' => '',
                    'append' => '',
                    'maxlength' => '',
                ),
                7 => array(
                    'key' => 'field_56c5e12ac81bc',
                    'label' => __('Lead', 'modularity'),
                    'name' => 'lead',
                    'type' => 'wysiwyg',
                    'instructions' => __('Om du inte vill använda den underliggande sidans ingress kan du skriva in en annan ingress här.', 'modularity'),
                    'required' => 0,
                    'conditional_logic' => array(
                        0 => array(
                            0 => array(
                                'field' => 'field_5743f66719b62',
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
                    'tabs' => 'all',
                    'toolbar' => 'full',
                    'media_upload' => 0,
                    'default_value' => '',
                    'delay' => 0,
                ),
                8 => array(
                    'key' => 'field_5743f6f619b65',
                    'label' => __('Lead', 'modularity'),
                    'name' => 'lead',
                    'type' => 'wysiwyg',
                    'instructions' => '',
                    'required' => 1,
                    'conditional_logic' => array(
                        0 => array(
                            0 => array(
                                'field' => 'field_5743f66719b62',
                                'operator' => '==',
                                'value' => 'external',
                            ),
                        ),
                        1 => array(
                            0 => array(
                                'field' => 'field_5743f66719b62',
                                'operator' => '==',
                                'value' => 'unlinked',
                            ),
                        ),
                    ),
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'default_value' => '',
                    'tabs' => 'all',
                    'toolbar' => 'full',
                    'media_upload' => 0,
                    'delay' => 0,
                ),
            ),
        ),
        1 => array(
            'key' => 'field_56eab26cd3a86',
            'label' => __('Columns', 'modularity'),
            'name' => 'index_columns',
            'type' => 'select',
            'instructions' => '',
            'required' => 1,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'multiple' => 0,
            'allow_null' => 0,
            'choices' => array(
                'grid-md-12' => __('1', 'modularity'),
                'grid-md-6' => __('2', 'modularity'),
                'grid-md-4' => __('3', 'modularity'),
                'grid-md-3' => __('4', 'modularity'),
            ),
            'default_value' => array(
                0 => __('grid-md-12', 'modularity'),
            ),
            'ui' => 0,
            'ajax' => 0,
            'placeholder' => '',
            'return_format' => 'value',
            'disabled' => 0,
            'readonly' => 0,
        ),
    ),
    'location' => array(
        0 => array(
            0 => array(
                'param' => 'post_type',
                'operator' => '==',
                'value' => 'mod-index',
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
));
}