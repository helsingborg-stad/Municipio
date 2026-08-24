<?php

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
        'key' => 'group_690dfe46d9b6e',
        'title' => __('Facetting', 'municipio'),
        'fields' => array(
            0 => array(
                'key' => 'field_690dfe47bba6b',
                'label' => __('Facetting', 'municipio'),
                'name' => 'algolia_index_facetting',
                'aria-label' => '',
                'type' => 'repeater',
                'instructions' => __(
                    'Provide the facetting configuration here. If it is left blank, no facetting will be displayed. Facettable options must be configured and enabled in the search provider. 
<ul>
 <li>The "attribute" should match a enabled facet in the search provider. </li>
 <li>The "label" is the value that will show up in the facetting panel for the user.</li>
</ul>
Note: Facetting feature requires the algolia JS search extension to work.',
                    'municipio',
                ),
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'layout' => 'table',
                'pagination' => 0,
                'min' => 0,
                'max' => 0,
                'collapsed' => 'field_690dfe6dbba6d',
                'button_label' => __('Add facetting property', 'municipio'),
                'rows_per_page' => 20,
                'sub_fields' => array(
                    0 => array(
                        'key' => 'field_690dfe56bba6c',
                        'label' => __('Attribute', 'municipio'),
                        'name' => 'attribute',
                        'aria-label' => '',
                        'type' => 'text',
                        'instructions' => '',
                        'required' => 0,
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
                        'parent_repeater' => 'field_690dfe47bba6b',
                    ),
                    1 => array(
                        'key' => 'field_690dfe6dbba6d',
                        'label' => __('Label', 'municipio'),
                        'name' => 'label',
                        'aria-label' => '',
                        'type' => 'text',
                        'instructions' => '',
                        'required' => 0,
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
                        'parent_repeater' => 'field_690dfe47bba6b',
                    ),
                    2 => array(
                        'key' => 'field_690e0517ced81',
                        'label' => __('Enabled', 'municipio'),
                        'name' => 'enabled',
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
                        'ui_on_text' => '',
                        'ui_off_text' => '',
                        'ui' => 1,
                        'parent_repeater' => 'field_690dfe47bba6b',
                    ),
                ),
            ),
        ),
        'location' => array(
            0 => array(
                0 => array(
                    'param' => 'options_page',
                    'operator' => '==',
                    'value' => 'algolia-index-settings',
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
        'show_in_rest' => 0,
    ));
}
