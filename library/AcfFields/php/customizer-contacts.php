<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_614336d78b898',
    'title' => __('Contacts', 'municipio'),
    'fields' => array(
        0 => array(
            'key' => 'field_6063008d5068a',
            'label' => __('Contacs - List - Style', 'municipio'),
            'name' => 'contacts-list-style',
            'type' => 'select',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'render_type' => 'filter',
            'filter_context' => 'module.contacts.list',
            'share_option' => 0,
            'choices' => array(
                'none' => __('None', 'municipio'),
                'panel' => __('Panel', 'municipio'),
                'accented' => __('Accented', 'municipio'),
                'highlight' => __('Highlight', 'municipio'),
            ),
            'default_value' => false,
            'allow_null' => 0,
            'multiple' => 0,
            'ui' => 0,
            'return_format' => 'value',
            'ajax' => 0,
            'placeholder' => '',
        ),
        1 => array(
            'key' => 'field_6090f318a40ef',
            'label' => __('Contacs - Card - Style', 'municipio'),
            'name' => 'contacts-card-style',
            'type' => 'select',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'render_type' => 'filter',
            'filter_context' => 'module.contacts.card',
            'share_option' => 0,
            'choices' => array(
                'none' => __('None', 'municipio'),
                'highlight' => __('Highlight', 'municipio'),
            ),
            'default_value' => false,
            'allow_null' => 0,
            'multiple' => 0,
            'ui' => 0,
            'return_format' => 'value',
            'ajax' => 0,
            'placeholder' => '',
        ),
    ),
    'location' => array(
        0 => array(
            0 => array(
                'param' => 'customizer',
                'operator' => '==',
                'value' => 'contacts',
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