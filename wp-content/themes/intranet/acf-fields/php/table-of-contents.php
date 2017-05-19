<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_5774dcb335058',
    'title' => __('Table of contents', 'municipio-intranet'),
    'fields' => array(
        0 => array(
            'sub_fields' => array(
                0 => array(
                    'default_value' => '',
                    'maxlength' => '',
                    'placeholder' => '',
                    'prepend' => '',
                    'append' => '',
                    'key' => 'field_57b57362e9ea0',
                    'label' => __(__(__(__(__('Title', 'municipio-intranet'), 'municipio-intranet'), 'municipio-intranet'), 'municipio-intranet'), 'municipio-intranet'),
                    'name' => 'title',
                    'type' => 'text',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                ),
            ),
            'min' => 0,
            'max' => 0,
            'layout' => 'table',
            'button_label' => __('Lägg till rad', 'municipio-intranet'),
            'collapsed' => '',
            'key' => 'field_5774dcc0432db',
            'label' => __('Title', 'municipio-intranet'),
            'name' => 'table_of_contents_titles',
            'type' => 'repeater',
            'instructions' => __('Sets the title for this page in the "table of contents" list. Leave empty to use page title.', 'municipio-intranet'),
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
                'param' => 'post_type',
                'operator' => '==',
                'value' => 'page',
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
));
}