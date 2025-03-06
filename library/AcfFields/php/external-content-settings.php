<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_66d94ae935cfb',
    'title' => __('External Content', 'municipio'),
    'fields' => array(
        0 => array(
            'key' => 'field_66da923803552',
            'label' => '',
            'name' => 'external_content_sources',
            'aria-label' => '',
            'type' => 'repeater',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => 'acf-admin-page',
                'id' => '',
            ),
            'acfe_repeater_stylised_button' => 1,
            'layout' => 'block',
            'pagination' => 0,
            'min' => 0,
            'max' => 0,
            'collapsed' => '',
            'button_label' => __('Add Source', 'municipio'),
            'rows_per_page' => 20,
            'sub_fields' => array(
                0 => array(
                    'key' => 'field_66da926c03553',
                    'label' => __('Post type', 'municipio'),
                    'name' => 'post_type',
                    'aria-label' => '',
                    'type' => 'select',
                    'instructions' => '',
                    'required' => 1,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '33',
                        'class' => '',
                        'id' => '',
                    ),
                    'choices' => array(
                    ),
                    'default_value' => false,
                    'return_format' => 'value',
                    'multiple' => 0,
                    'allow_null' => 0,
                    'allow_in_bindings' => 1,
                    'ui' => 0,
                    'ajax' => 0,
                    'placeholder' => '',
                    'allow_custom' => 0,
                    'search_placeholder' => '',
                    'parent_repeater' => 'field_66da923803552',
                ),
                1 => array(
                    'key' => 'field_66da92a003554',
                    'label' => __('Source type', 'municipio'),
                    'name' => 'source_type',
                    'aria-label' => '',
                    'type' => 'select',
                    'instructions' => '',
                    'required' => 1,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '33',
                        'class' => '',
                        'id' => '',
                    ),
                    'choices' => array(
                        'json' => __('json', 'municipio'),
                        'typesense' => __('typesense', 'municipio'),
                    ),
                    'default_value' => __('json', 'municipio'),
                    'return_format' => 'value',
                    'multiple' => 0,
                    'allow_null' => 0,
                    'allow_in_bindings' => 1,
                    'ui' => 0,
                    'ajax' => 0,
                    'placeholder' => '',
                    'allow_custom' => 0,
                    'search_placeholder' => '',
                    'parent_repeater' => 'field_66da923803552',
                ),
                2 => array(
                    'key' => 'field_66da9961f781e',
                    'label' => __('Automatic import schedule', 'municipio'),
                    'name' => 'automatic_import_schedule',
                    'aria-label' => '',
                    'type' => 'select',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '34',
                        'class' => '',
                        'id' => '',
                    ),
                    'choices' => array(
                    ),
                    'default_value' => false,
                    'return_format' => 'value',
                    'multiple' => 0,
                    'allow_null' => 0,
                    'allow_in_bindings' => 1,
                    'ui' => 0,
                    'ajax' => 0,
                    'placeholder' => '',
                    'allow_custom' => 0,
                    'search_placeholder' => '',
                    'parent_repeater' => 'field_66da923803552',
                ),
                3 => array(
                    'key' => 'field_66da92db03555',
                    'label' => __('File path', 'municipio'),
                    'name' => 'source_json_file_path',
                    'aria-label' => '',
                    'type' => 'text',
                    'instructions' => '',
                    'required' => 1,
                    'conditional_logic' => array(
                        0 => array(
                            0 => array(
                                'field' => 'field_66da92a003554',
                                'operator' => '==',
                                'value' => 'json',
                            ),
                        ),
                    ),
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'default_value' => '',
                    'maxlength' => '',
                    'placeholder' => '',
                    'prepend' => '',
                    'append' => '',
                    'parent_repeater' => 'field_66da923803552',
                ),
                4 => array(
                    'key' => 'field_66da93814c9a4',
                    'label' => __('Protocol', 'municipio'),
                    'name' => 'source_typesense_protocol',
                    'aria-label' => '',
                    'type' => 'select',
                    'instructions' => '',
                    'required' => 1,
                    'conditional_logic' => array(
                        0 => array(
                            0 => array(
                                'field' => 'field_66da92a003554',
                                'operator' => '==',
                                'value' => 'typesense',
                            ),
                        ),
                    ),
                    'wrapper' => array(
                        'width' => '11',
                        'class' => '',
                        'id' => '',
                    ),
                    'choices' => array(
                        'https' => __('https', 'municipio'),
                        'http' => __('http', 'municipio'),
                    ),
                    'default_value' => __('https', 'municipio'),
                    'return_format' => 'value',
                    'multiple' => 0,
                    'allow_null' => 0,
                    'allow_in_bindings' => 1,
                    'ui' => 0,
                    'ajax' => 0,
                    'placeholder' => '',
                    'allow_custom' => 0,
                    'search_placeholder' => '',
                    'parent_repeater' => 'field_66da923803552',
                ),
                5 => array(
                    'key' => 'field_66da93a44c9a5',
                    'label' => __('Host', 'municipio'),
                    'name' => 'source_typesense_host',
                    'aria-label' => '',
                    'type' => 'text',
                    'instructions' => '',
                    'required' => 1,
                    'conditional_logic' => array(
                        0 => array(
                            0 => array(
                                'field' => 'field_66da92a003554',
                                'operator' => '==',
                                'value' => 'typesense',
                            ),
                        ),
                    ),
                    'wrapper' => array(
                        'width' => '26',
                        'class' => '',
                        'id' => '',
                    ),
                    'default_value' => '',
                    'maxlength' => '',
                    'allow_in_bindings' => 1,
                    'placeholder' => '',
                    'prepend' => '',
                    'append' => '',
                    'parent_repeater' => 'field_66da923803552',
                ),
                6 => array(
                    'key' => 'field_66da93cc4c9a6',
                    'label' => __('Port', 'municipio'),
                    'name' => 'source_typesense_port',
                    'aria-label' => '',
                    'type' => 'text',
                    'instructions' => '',
                    'required' => 1,
                    'conditional_logic' => array(
                        0 => array(
                            0 => array(
                                'field' => 'field_66da92a003554',
                                'operator' => '==',
                                'value' => 'typesense',
                            ),
                        ),
                    ),
                    'wrapper' => array(
                        'width' => '11',
                        'class' => '',
                        'id' => '',
                    ),
                    'default_value' => '',
                    'maxlength' => '',
                    'allow_in_bindings' => 1,
                    'placeholder' => '',
                    'prepend' => '',
                    'append' => '',
                    'parent_repeater' => 'field_66da923803552',
                ),
                7 => array(
                    'key' => 'field_66da93e44c9a7',
                    'label' => __('Collection', 'municipio'),
                    'name' => 'source_typesense_collection',
                    'aria-label' => '',
                    'type' => 'text',
                    'instructions' => '',
                    'required' => 1,
                    'conditional_logic' => array(
                        0 => array(
                            0 => array(
                                'field' => 'field_66da92a003554',
                                'operator' => '==',
                                'value' => 'typesense',
                            ),
                        ),
                    ),
                    'wrapper' => array(
                        'width' => '26',
                        'class' => '',
                        'id' => '',
                    ),
                    'default_value' => '',
                    'maxlength' => '',
                    'allow_in_bindings' => 1,
                    'placeholder' => '',
                    'prepend' => '',
                    'append' => '',
                    'parent_repeater' => 'field_66da923803552',
                ),
                8 => array(
                    'key' => 'field_66da933b4c9a3',
                    'label' => __('API key', 'municipio'),
                    'name' => 'source_typesense_api_key',
                    'aria-label' => '',
                    'type' => 'text',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => array(
                        0 => array(
                            0 => array(
                                'field' => 'field_66da92a003554',
                                'operator' => '==',
                                'value' => 'typesense',
                            ),
                        ),
                    ),
                    'wrapper' => array(
                        'width' => '26',
                        'class' => '',
                        'id' => '',
                    ),
                    'default_value' => '',
                    'maxlength' => '',
                    'allow_in_bindings' => 1,
                    'placeholder' => '',
                    'prepend' => '',
                    'append' => '',
                    'parent_repeater' => 'field_66da923803552',
                ),
                9 => array(
                    'key' => 'field_66da99ce96264',
                    'label' => __('Taxonomies', 'municipio'),
                    'name' => 'taxonomies',
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
                    'min' => 0,
                    'max' => 0,
                    'collapsed' => '',
                    'button_label' => __('Add Taxonomy', 'municipio'),
                    'rows_per_page' => 20,
                    'parent_repeater' => 'field_66da923803552',
                    'sub_fields' => array(
                        0 => array(
                            'key' => 'field_66da99ea96265',
                            'label' => __('Create terms from schema property', 'municipio'),
                            'name' => 'from_schema_property',
                            'aria-label' => '',
                            'type' => 'text',
                            'instructions' => __('Name of the schema property to use as a taxonomy. Ex. for using the <code>director</code> property on the <code>Event</code> schema type, supply <code>director</code> as value. If you wish to use a nested object value you can nest like this: <code>director.email</code>. Should the property contain an object of the type <a href="https://schema.org/PropertyValue" target="_blank">PropertyValue</a> the <code>value</code> property will be used by default when creating terms.', 'municipio'),
                            'required' => 1,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '',
                                'class' => '',
                                'id' => '',
                            ),
                            'default_value' => '',
                            'maxlength' => '',
                            'placeholder' => __('ex. director', 'municipio'),
                            'prepend' => '',
                            'append' => '',
                            'parent_repeater' => 'field_66da99ce96264',
                        ),
                        1 => array(
                            'key' => 'field_66da9bc696266',
                            'label' => __('Name (plural)', 'municipio'),
                            'name' => 'name',
                            'aria-label' => '',
                            'type' => 'text',
                            'instructions' => '',
                            'required' => 1,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '',
                                'class' => '',
                                'id' => '',
                            ),
                            'default_value' => '',
                            'maxlength' => '',
                            'placeholder' => '',
                            'prepend' => '',
                            'append' => '',
                            'parent_repeater' => 'field_66da99ce96264',
                        ),
                        2 => array(
                            'key' => 'field_66da9be796267',
                            'label' => __('Name (singular)', 'municipio'),
                            'name' => 'singular_name',
                            'aria-label' => '',
                            'type' => 'text',
                            'instructions' => '',
                            'required' => 1,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '',
                                'class' => '',
                                'id' => '',
                            ),
                            'default_value' => '',
                            'maxlength' => '',
                            'placeholder' => '',
                            'prepend' => '',
                            'append' => '',
                            'parent_repeater' => 'field_66da99ce96264',
                        ),
                        3 => array(
                            'key' => 'field_672b0a32bd0e0',
                            'label' => __('Hierarchical', 'municipio'),
                            'name' => 'hierarchical',
                            'aria-label' => '',
                            'type' => 'true_false',
                            'instructions' => '',
                            'required' => 0,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '',
                                'class' => '',
                                'id' => '',
                            ),
                            'message' => '',
                            'default_value' => 0,
                            'allow_in_bindings' => 1,
                            'ui' => 0,
                            'ui_on_text' => '',
                            'ui_off_text' => '',
                            'parent_repeater' => 'field_66da99ce96264',
                        ),
                    ),
                ),
                10 => array(
                    'key' => 'field_67c940e8a3f5d',
                    'label' => __('Filters rules', 'municipio'),
                    'name' => 'rules',
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
                    'min' => 0,
                    'max' => 0,
                    'collapsed' => '',
                    'button_label' => __('Add filter rule', 'municipio'),
                    'rows_per_page' => 20,
                    'sub_fields' => array(
                        0 => array(
                            'key' => 'field_67c9411ba3f5e',
                            'label' => __('Property path', 'municipio'),
                            'name' => 'property_path',
                            'aria-label' => '',
                            'type' => 'text',
                            'instructions' => '',
                            'required' => 1,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '40',
                                'class' => '',
                                'id' => '',
                            ),
                            'default_value' => '',
                            'maxlength' => '',
                            'allow_in_bindings' => 0,
                            'placeholder' => '',
                            'prepend' => '',
                            'append' => '',
                            'parent_repeater' => 'field_67c940e8a3f5d',
                        ),
                        1 => array(
                            'key' => 'field_67c9413ba3f5f',
                            'label' => __('Operator', 'municipio'),
                            'name' => 'operator',
                            'aria-label' => '',
                            'type' => 'select',
                            'instructions' => '',
                            'required' => 0,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '20',
                                'class' => '',
                                'id' => '',
                            ),
                            'choices' => array(
                                'EQUALS' => __('is equal to', 'municipio'),
                                'NOT_EQUALS' => __('is not equal to', 'municipio'),
                            ),
                            'default_value' => __('EQUALS', 'municipio'),
                            'return_format' => 'value',
                            'multiple' => 0,
                            'allow_null' => 0,
                            'allow_in_bindings' => 0,
                            'ui' => 0,
                            'ajax' => 0,
                            'placeholder' => '',
                            'allow_custom' => 0,
                            'search_placeholder' => '',
                            'parent_repeater' => 'field_67c940e8a3f5d',
                        ),
                        2 => array(
                            'key' => 'field_67c941a5a3f60',
                            'label' => __('value', 'municipio'),
                            'name' => 'value',
                            'aria-label' => '',
                            'type' => 'text',
                            'instructions' => '',
                            'required' => 1,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '40',
                                'class' => '',
                                'id' => '',
                            ),
                            'default_value' => '',
                            'maxlength' => '',
                            'allow_in_bindings' => 0,
                            'placeholder' => '',
                            'prepend' => '',
                            'append' => '',
                            'parent_repeater' => 'field_67c940e8a3f5d',
                        ),
                    ),
                    'parent_repeater' => 'field_66da923803552',
                ),
            ),
        ),
    ),
    'location' => array(
        0 => array(
            0 => array(
                'param' => 'options_page',
                'operator' => '==',
                'value' => 'mun-external-content-settings',
            ),
        ),
    ),
    'menu_order' => 0,
    'position' => 'acf_after_title',
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