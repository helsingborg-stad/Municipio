<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_56cacd2f1873f',
    'title' => 'Timestamp',
    'fields' => array(
        0 => array(
            'message' => __('Check boxes to enable timestamps for post types. Will be displayed after the content area, if applicable.', 'municipio'),
            'esc_html' => 0,
            'new_lines' => 'wpautop',
            'key' => 'field_56cacece474bf',
            'label' => __('Post footer timestamps', 'municipio'),
            'name' => '',
            'type' => 'message',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
        ),
        1 => array(
            'layout' => 'horizontal',
            'choices' => array(
                'post' => 'Inlägg',
                'page' => 'Sidor',
                'attachment' => 'Media',
                'listing' => 'Annonser',
            ),
            'default_value' => array(
            ),
            'allow_custom' => 0,
            'save_custom' => 0,
            'toggle' => 0,
            'return_format' => 'value',
            'key' => 'field_56cacd3332c2a',
            'label' => __('Date published', 'municipio'),
            'name' => 'show_date_published',
            'type' => 'checkbox',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => 50,
                'class' => '',
                'id' => '',
            ),
        ),
        2 => array(
            'layout' => 'horizontal',
            'choices' => array(
                'post' => 'Inlägg',
                'page' => 'Sidor',
                'attachment' => 'Media',
                'listing' => 'Annonser',
            ),
            'default_value' => array(
            ),
            'allow_custom' => 0,
            'save_custom' => 0,
            'toggle' => 0,
            'return_format' => 'value',
            'key' => 'field_56cacde7afe46',
            'label' => __('Date updated', 'municipio'),
            'name' => 'show_date_updated',
            'type' => 'checkbox',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => 50,
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
    'local' => 'php',
));
}