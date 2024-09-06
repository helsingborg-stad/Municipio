<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_66d94a4867cec',
    'title' => __('Post types Schema settings', 'municipio'),
    'fields' => array(
        0 => array(
            'key' => 'field_66da9e0affa64',
            'label' => __('Post type schema', 'municipio'),
            'name' => 'post_type_schema_types',
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
            'acfe_repeater_stylised_button' => 1,
            'layout' => 'block',
            'pagination' => 0,
            'min' => 0,
            'max' => 0,
            'collapsed' => '',
            'button_label' => __('Add Row', 'municipio'),
            'rows_per_page' => 20,
            'sub_fields' => array(
                0 => array(
                    'key' => 'field_66da9e38ffa65',
                    'label' => __('Post type', 'municipio'),
                    'name' => 'post_type',
                    'aria-label' => '',
                    'type' => 'posttype_select',
                    'instructions' => '',
                    'required' => 1,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '50',
                        'class' => '',
                        'id' => '',
                    ),
                    'default_value' => '',
                    'allow_null' => 0,
                    'multiple' => 0,
                    'placeholder' => '',
                    'disabled' => 0,
                    'readonly' => 0,
                    'parent_repeater' => 'field_66da9e0affa64',
                ),
                1 => array(
                    'key' => 'field_66da9e4dffa66',
                    'label' => __('Schema type', 'municipio'),
                    'name' => 'schema_type',
                    'aria-label' => '',
                    'type' => 'select',
                    'instructions' => '',
                    'required' => 1,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '50',
                        'class' => '',
                        'id' => '',
                    ),
                    'choices' => array(
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
                    'parent_repeater' => 'field_66da9e0affa64',
                ),
            ),
        ),
    ),
    'location' => array(
        0 => array(
            0 => array(
                'param' => 'options_page',
                'operator' => '==',
                'value' => 'mun-post-type-schema-settings',
            ),
        ),
    ),
    'menu_order' => 0,
    'position' => 'normal',
    'style' => 'seamless',
    'label_placement' => 'top',
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