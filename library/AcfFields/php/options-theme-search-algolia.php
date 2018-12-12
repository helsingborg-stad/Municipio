<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_5a61b852f3f8c',
    'title' => 'Algolia Sök',
    'fields' => array(
        0 => array(
            'key' => 'field_5a61b85c6e7b8',
            'label' => 'Enable',
            'name' => 'use_algolia_search',
            'type' => 'true_false',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'message' => 'Enable the Algolia Search (Requires Algolia WordPress plugin)',
            'default_value' => 0,
            'ui' => 0,
            'ui_on_text' => '',
            'ui_off_text' => '',
        ),
        1 => array(
            'key' => 'field_5b3c6dc1c3210',
            'label' => __('Visa etikett med posttyp', 'municipio'),
            'name' => 'algolia_display_post_types',
            'type' => 'posttype_select',
            'instructions' => __('Visar en etikett för utvalda posttyper med deras namn bland sökresultaten.', 'municipio'),
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'default_value' => '',
            'allow_null' => 1,
            'multiple' => 1,
            'placeholder' => '',
            'disabled' => 0,
            'readonly' => 0,
        ),
        2 => array(
            'key' => 'field_5c111b35a3803',
            'label' => __('Search didn\'t match query message', 'municipio'),
            'name' => 'search_didnt_match_query_message',
            'type' => 'text',
            'instructions' => __('Add a custom query message, if the search doesn\'t match query (Autocomplete).', 'municipio'),
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'default_value' => '',
            'placeholder' => __('Lägg till meddelande…', 'municipio'),
            'prepend' => '',
            'append' => '',
            'maxlength' => '',
        ),
    ),
    'location' => array(
        0 => array(
            0 => array(
                'param' => 'options_page',
                'operator' => '==',
                'value' => 'acf-options-search',
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
));
}