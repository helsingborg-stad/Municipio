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
        'key' => 'group_municipio_search_index_algolia_settings',
        'title' => __('Algolia Provider Settings', 'municipio'),
        'fields' => [[
            'key' => 'field_municipio_search_index_algolia_application_id',
            'label' => __('Application ID', 'municipio'),
            'name' => 'search_index_algolia_application_id',
            'type' => 'text',
            'instructions' => __('May be overridden by SEARCH_INDEX_ALGOLIA_APPLICATION_ID.', 'municipio'),
        ], [
            'key' => 'field_municipio_search_index_algolia_api_key',
            'label' => __('API Key', 'municipio'),
            'name' => 'search_index_algolia_api_key',
            'type' => 'password',
            'instructions' => __('May be overridden by SEARCH_INDEX_ALGOLIA_API_KEY.', 'municipio'),
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