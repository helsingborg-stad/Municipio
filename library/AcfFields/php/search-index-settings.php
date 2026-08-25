<?php

declare(strict_types=1);

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group([
        'key' => 'group_municipio_search_index_settings',
        'title' => __('Search Index Settings', 'municipio'),
        'fields' => [[
            'key' => 'field_municipio_search_index_provider',
            'label' => __('Search Provider', 'municipio'),
            'name' => 'search_index_provider',
            'type' => 'select',
            'choices' => ['algolia' => __('Algolia', 'municipio')],
            'default_value' => 'algolia',
            'return_format' => 'value',
            'allow_null' => 0,
        ], [
            'key' => 'field_municipio_search_index_name',
            'label' => __('Index Name', 'municipio'),
            'name' => 'search_index_name',
            'type' => 'text',
            'instructions' => __('Uses the site hostname when empty. May be overridden by SEARCH_INDEX_NAME.', 'municipio'),
        ], [
            'key' => 'field_municipio_search_index_algolia_application_id',
            'label' => __('Algolia Application ID', 'municipio'),
            'name' => 'search_index_algolia_application_id',
            'type' => 'text',
            'instructions' => __('May be overridden by SEARCH_INDEX_ALGOLIA_APPLICATION_ID.', 'municipio'),
            'conditional_logic' => [[[
                'field' => 'field_municipio_search_index_provider',
                'operator' => '==',
                'value' => 'algolia',
            ]]],
        ], [
            'key' => 'field_municipio_search_index_algolia_api_key',
            'label' => __('Algolia API Key', 'municipio'),
            'name' => 'search_index_algolia_api_key',
            'type' => 'password',
            'instructions' => __('May be overridden by SEARCH_INDEX_ALGOLIA_API_KEY.', 'municipio'),
            'conditional_logic' => [[[
                'field' => 'field_municipio_search_index_provider',
                'operator' => '==',
                'value' => 'algolia',
            ]]],
        ], [
            'key' => 'field_municipio_search_index_algolia_public_api_key',
            'label' => __('Algolia Public API Key', 'municipio'),
            'name' => 'search_index_algolia_public_api_key',
            'type' => 'password',
            'instructions' => __('Optional browser-search key. May be overridden by SEARCH_INDEX_ALGOLIA_PUBLIC_API_KEY.', 'municipio'),
            'conditional_logic' => [[[
                'field' => 'field_municipio_search_index_provider',
                'operator' => '==',
                'value' => 'algolia',
            ]]],
        ], [
            'key' => 'field_municipio_search_index_typesense_api_url',
            'label' => __('Typesense API URL', 'municipio'),
            'name' => 'search_index_typesense_api_url',
            'type' => 'url',
            'instructions' => __('May be overridden by SEARCH_INDEX_TYPESENSE_API_URL.', 'municipio'),
            'conditional_logic' => [[[
                'field' => 'field_municipio_search_index_provider',
                'operator' => '==',
                'value' => 'typesense',
            ]]],
        ], [
            'key' => 'field_municipio_search_index_typesense_api_key',
            'label' => __('Typesense API Key', 'municipio'),
            'name' => 'search_index_typesense_api_key',
            'type' => 'password',
            'instructions' => __('May be overridden by SEARCH_INDEX_TYPESENSE_API_KEY.', 'municipio'),
            'conditional_logic' => [[[
                'field' => 'field_municipio_search_index_provider',
                'operator' => '==',
                'value' => 'typesense',
            ]]],
        ], [
            'key' => 'field_municipio_search_index_typesense_public_api_key',
            'label' => __('Typesense Public API Key', 'municipio'),
            'name' => 'search_index_typesense_public_api_key',
            'type' => 'password',
            'instructions' => __('Optional browser-search key. May be overridden by SEARCH_INDEX_TYPESENSE_PUBLIC_API_KEY.', 'municipio'),
            'conditional_logic' => [[[
                'field' => 'field_municipio_search_index_provider',
                'operator' => '==',
                'value' => 'typesense',
            ]]],
        ], [
            'key' => 'field_municipio_search_index_typesense_collection_name',
            'label' => __('Typesense Collection Name', 'municipio'),
            'name' => 'search_index_typesense_collection_name',
            'type' => 'text',
            'instructions' => __('Uses the configured index name when empty. May be overridden by SEARCH_INDEX_TYPESENSE_COLLECTION_NAME.', 'municipio'),
            'conditional_logic' => [[[
                'field' => 'field_municipio_search_index_provider',
                'operator' => '==',
                'value' => 'typesense',
            ]]],
        ]],
        'location' => [[[
            'param' => 'options_page',
            'operator' => '==',
            'value' => 'municipio-search-index-settings',
        ]]],
        'position' => 'normal',
        'style' => 'default',
        'active' => true,
    ]);

    acf_add_local_field_group([
        'key' => 'group_municipio_search_index_facet_settings',
        'title' => __('Search Facets', 'municipio'),
        'fields' => [[
            'key' => 'field_municipio_search_index_facets',
            'label' => __('Facets', 'municipio'),
            'name' => 'search_index_facets',
            'type' => 'repeater',
            'instructions' => __('Configure the provider facet attributes available to search UI integrations.', 'municipio'),
            'layout' => 'table',
            'button_label' => __('Add Facet', 'municipio'),
            'sub_fields' => [[
                'key' => 'field_municipio_search_index_facet_attribute',
                'label' => __('Attribute', 'municipio'),
                'name' => 'attribute',
                'type' => 'text',
            ], [
                'key' => 'field_municipio_search_index_facet_label',
                'label' => __('Label', 'municipio'),
                'name' => 'label',
                'type' => 'text',
            ], [
                'key' => 'field_municipio_search_index_facet_enabled',
                'label' => __('Enabled', 'municipio'),
                'name' => 'enabled',
                'type' => 'true_false',
                'ui' => 1,
                'default_value' => 0,
            ]],
        ]],
        'location' => [[[
            'param' => 'options_page',
            'operator' => '==',
            'value' => 'municipio-search-index-settings',
        ]]],
        'position' => 'normal',
        'style' => 'default',
        'active' => true,
    ]);
}