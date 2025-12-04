<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_64227d79a7f57',
    'title' => __('Quicklinks', 'municipio'),
    'fields' => array(
        0 => array(
            'key' => 'field_64227ca019e18',
            'label' => __('Placement', 'municipio'),
            'name' => 'quicklinks_placement',
            'aria-label' => '',
            'type' => 'select',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'choices' => array(
                'default' => __('Default', 'municipio'),
                'after_first_block' => __('After first block', 'municipio'),
                'below_content' => __('Below page content', 'municipio'),
            ),
            'default_value' => __('default', 'municipio'),
            'return_format' => 'value',
            'multiple' => 0,
            'allow_custom' => 0,
            'search_placeholder' => '',
            'allow_null' => 0,
            'ui' => 1,
            'ajax' => 0,
            'placeholder' => '',
        ),
    ),
    'location' => array(
        0 => array(
            0 => array(
                'param' => 'post_type',
                'operator' => '==',
                'value' => 'all',
            ),
        ),
    ),
    'menu_order' => 0,
    'position' => 'side',
    'style' => 'default',
    'label_placement' => 'left',
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