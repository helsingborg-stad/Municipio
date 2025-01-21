<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_678e65a73edb3',
    'title' => 'Common Field Groups',
    'fields' => array(
        0 => array(
            'key' => 'field_678fb6b1caa9e',
            'label' => __('Common Field Groups Feature Description', 'municipio'),
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
            'message' => __('This feature allows you to select fields that should be manage from the main site id (that is this site). Field groups will not be rendered on the subsites; Instead a link will be rendered to take the user to the main site for editing of the option. 

Please select the field groups that you want to manage centrally below.', 'municipio'),
            'new_lines' => 'wpautop',
            'esc_html' => 0,
        ),
        1 => array(
            'key' => 'field_678e65abcf203',
            'label' => __('Common Field Groups', 'municipio'),
            'name' => 'sitewide_common_acf_fieldgroups',
            'aria-label' => '',
            'type' => 'repeater',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'acfe_repeater_stylised_button' => 0,
            'layout' => 'table',
            'pagination' => 0,
            'min' => 0,
            'max' => 0,
            'collapsed' => '',
            'button_label' => __('Lägg till rad', 'municipio'),
            'rows_per_page' => 20,
            'sub_fields' => array(
                0 => array(
                    'key' => 'field_678e6610d5287',
                    'label' => __('Select a fieldgroup', 'municipio'),
                    'name' => 'sitewide_common_acf_fieldgroup_value',
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
                        'group_678e65a73edb3' => __('Common Field Groups', 'municipio'),
                        'group_5aa1543e70216' => __('Report settings', 'municipio'),
                    ),
                    'default_value' => false,
                    'return_format' => 'value',
                    'multiple' => 0,
                    'allow_null' => 0,
                    'ui' => 0,
                    'ajax' => 0,
                    'placeholder' => '',
                    'allow_custom' => 0,
                    'search_placeholder' => '',
                    'parent_repeater' => 'field_678e65abcf203',
                ),
            ),
        ),
    ),
    'location' => array(
        0 => array(
            0 => array(
                'param' => 'options_page',
                'operator' => '==',
                'value' => 'common-field-groups',
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