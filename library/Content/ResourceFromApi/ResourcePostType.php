<?php

namespace Municipio\Content\ResourceFromApi;

use Municipio\Helper\RestRequestHelper;

class ResourcePostType
{

    public function addHooks(): void
    {
        add_action('init', [$this, 'addPostType']);
        add_action('acf/init', [$this, 'addOptionsPage']);
        add_filter('acf/load_field/name=post_type_source', [$this, 'loadPostTypeSourceOptions']);
        add_filter('acf/load_field/name=taxonomy_source', [$this, 'loadTaxonomySourceOptions']);
        add_action('acf/save_post', [$this, 'setResourcePostTitleFromAcf'], 10);
        add_filter('acf/update_value/name=post_type_key', [$this, 'sanitizePostTypeKeyBeforeSave'], 10, 4);
    }

    public function addPostType()
    {
        register_post_type(
            'api-resource',
            [
                'label' => __('API Resources', 'municipio'),
                'labels' => [
                    'singular_name' => __('API Resource', 'municipio'),
                ],
                'show_ui' => true,
                'public' => false,
                'has_archive' => false,
                'show_in_rest' => false,
                'supports' => false,
                'taxonomies' => [],
            ]
        );
    }

    public function addOptionsPage()
    {
        if (function_exists('acf_add_options_page')) {
            acf_add_options_page(array(
                'page_title'    => 'Api:s',
                'menu_title'    => 'Api:s',
                'menu_slug'     => 'api-resource-apis',
                'capability'    => 'manage_options',
                'redirect'       => false,
                'parent_slug' => 'edit.php?post_type=api-resource',
            ));
        }
    }

    function loadPostTypeSourceOptions($field)
    {

        $choices = [];

        if (!function_exists('get_field')) {
            return $field;
        }

        $endpoints = get_field('api_resources_apis', 'options');

        if (empty($endpoints)) {
            return $field;
        }

        $urls = array_map(fn ($row) => $row['url'], $endpoints);
        $urls = array_filter($urls, fn ($url) => filter_var($url, FILTER_VALIDATE_URL) !== false);

        if (empty($urls)) {
            return $field;
        }

        foreach ($urls as $url) {

            $typesFromApi = RestRequestHelper::getFromApi(trailingslashit($url) . 'types');

            if (is_wp_error($typesFromApi) || empty($typesFromApi)) {
                return null;
            }

            foreach ($typesFromApi as $type) {
                if (
                    !isset($type->slug) ||
                    !isset($type->_links) ||
                    !isset($type->_links->collection) ||
                    empty($type->_links->collection) ||
                    !isset($type->_links->collection[0]->href) ||
                    !filter_var($type->_links->collection[0]->href, FILTER_VALIDATE_URL)
                ) {
                    continue;
                }

                $value = trailingslashit($url) . $type->rest_base;
                $label = "{$type->slug}: {$value}";
                $choices[$value] = $label;
            }
        }

        if (!empty($choices)) {
            $field['choices'] = $choices;
        }

        return $field;
    }

    function loadTaxonomySourceOptions($field)
    {

        $choices = [];

        if (!function_exists('get_field')) {
            return $field;
        }

        $endpoints = get_field('api_resources_apis', 'options');

        if (empty($endpoints)) {
            return $field;
        }

        $urls = array_map(fn ($row) => $row['url'], $endpoints);
        $urls = array_filter($urls, fn ($url) => filter_var($url, FILTER_VALIDATE_URL) !== false);

        if (empty($urls)) {
            return $field;
        }

        foreach ($urls as $url) {

            $typesFromApi = RestRequestHelper::getFromApi(trailingslashit($url) . 'taxonomies');

            if (is_wp_error($typesFromApi) || empty($typesFromApi)) {
                return null;
            }

            foreach ($typesFromApi as $type) {
                if (
                    !isset($type->slug) ||
                    !isset($type->_links) ||
                    !isset($type->_links->collection) ||
                    empty($type->_links->collection) ||
                    !isset($type->_links->collection[0]->href) ||
                    !filter_var($type->_links->collection[0]->href, FILTER_VALIDATE_URL)
                ) {
                    continue;
                }

                $value = trailingslashit($url) . $type->rest_base;
                $label = "{$type->slug}: {$value}";
                $choices[$value] = $label;
            }
        }

        if (!empty($choices)) {
            $field['choices'] = $choices;
        }

        return $field;
    }

    public function setResourcePostTitleFromAcf($postId)
    {
        $postTypeArguments = get_field('post_type_arguments', $postId);

        if (
            empty($postTypeArguments) ||
            !isset($postTypeArguments['post_type_key']) ||
            empty($postTypeArguments['post_type_key'])
        ) {
            return;
        }

        $postTypeName = $postTypeArguments['post_type_key'];
        $postTypeName = sanitize_title(substr($postTypeName, 0, 19));

        wp_update_post([
            'ID' => $postId,
            'post_title' => $postTypeName,
            'post_name' => $postTypeName,

        ]);
    }

    public function sanitizePostTypeKeyBeforeSave($value, $postId, $field, $originalValue)
    {
        return sanitize_title(substr($value, 0, 19));
    }
}
