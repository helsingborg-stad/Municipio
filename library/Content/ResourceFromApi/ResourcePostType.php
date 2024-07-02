<?php

namespace Municipio\Content\ResourceFromApi;

use Municipio\Helper\RestRequestHelper;

/**
 * Class ResourcePostType
 * Registers the post type for the resource.
 */
class ResourcePostType
{
    public const POST_TYPE_NAME = 'api-resource';

    /**
     * Adds hooks for the ResourcePostType class.
     *
     * @return void
     */
    public function addHooks(): void
    {
        add_action('init', [$this, 'addPostType']);
        add_action('init', [$this, 'addOptionsPage']);
        add_filter('acf/load_field/name=post_type_source', [$this, 'loadPostTypeSourceOptions']);
        add_filter('acf/load_field/name=taxonomy_source', [$this, 'loadTaxonomySourceOptions']);
        add_filter('acf/load_field/name=attachment_source', [$this, 'loadAttachmentSourceOptions']);
        add_filter('acf/load_field/key=field_655878bfc1a9a', [$this, 'loadAttachmentArgumentsPostTypes']);
        add_action('acf/save_post', [$this, 'setPostTypeResourcePostTitleFromAcf'], 10);
        add_action('acf/save_post', [$this, 'setTaxonomyResourcePostTitleFromAcf'], 10);
        add_action('acf/save_post', [$this, 'setAttachmentResourcePostTitleFromAcf'], 10);
        add_action('acf/save_post', [$this, 'setApiMeta'], 10);
        add_filter('acf/update_value/name=post_type_key', [$this, 'sanitizePostTypeKeyBeforeSave'], 10, 4);
        add_filter('acf/update_value/name=taxonomy_key', [$this, 'sanitizeTaxonomyKeyBeforeSave'], 10, 4);
    }

    /**
     * Adds a custom post type for API resources.
     *
     * @return void
     */
    public function addPostType(): void
    {
        register_post_type(
            self::POST_TYPE_NAME,
            [
                'label'        => __('API Resources', 'municipio'),
                'labels'       => [
                    'singular_name' => __('API Resource', 'municipio'),
                ],
                'show_ui'      => true,
                'public'       => false,
                'has_archive'  => false,
                'show_in_rest' => false,
                'supports'     => false,
                'taxonomies'   => [],
            ]
        );
    }

    /**
     * Adds an options page for managing API resources.
     *
     * @return void
     */
    public function addOptionsPage(): void
    {
        if (function_exists('acf_add_options_page')) {
            acf_add_options_page(array(
                'page_title'  => 'Api:s',
                'menu_title'  => 'Api:s',
                'menu_slug'   => 'api-resource-apis',
                'capability'  => 'manage_options',
                'redirect'    => false,
                'parent_slug' => 'edit.php?post_type=api-resource',
            ));
        }
    }

    /**
     * Loads the source options for the post type field.
     *
     * @param array $field The field array.
     * @return array|null The modified field array with source options or null if an error occurs.
     */
    public function loadPostTypeSourceOptions($field): ?array
    {
        $choices = [];

        if (!function_exists('get_field')) {
            return $field;
        }

        $endpoints = get_field('api_resources_apis', 'options');

        if (!is_array($endpoints) || empty($endpoints)) {
            return $field;
        }

        $urls = array_map(fn ($row) => $row['url'], $endpoints);
        $urls = array_filter($urls, fn ($url) => filter_var($url, FILTER_VALIDATE_URL) !== false);

        if (empty($urls)) {
            return $field;
        }

        foreach ($urls as $url) {
            $typesFromApi = RestRequestHelper::get(trailingslashit($url) . 'types');

            if (is_wp_error($typesFromApi) || empty($typesFromApi)) {
                continue;
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

                $value            = trailingslashit($url) . ",{$type->slug}" . ",{$type->rest_base}";
                $labelParenthesis = trailingslashit($url) . $type->rest_base;
                $label            = "{$type->slug}: {$labelParenthesis}";
                $choices[$value]  = $label;
            }
        }

        if (!empty($choices)) {
            $field['choices'] = $choices;
        }

        return $field;
    }

    /**
     * Loads taxonomy source options for a given field.
     *
     * @param array $field The field to load taxonomy source options for.
     * @return array|null The modified field with loaded taxonomy source options, or null if an error occurred.
     */
    public function loadTaxonomySourceOptions($field): ?array
    {

        $choices = [];

        if (!function_exists('get_field')) {
            return $field;
        }

        $endpoints = get_field('api_resources_apis', 'options');

        if (!is_array($endpoints) || empty($endpoints)) {
            return $field;
        }

        $urls = array_map(fn ($row) => $row['url'], $endpoints);
        $urls = array_filter($urls, fn ($url) => filter_var($url, FILTER_VALIDATE_URL) !== false);

        if (empty($urls)) {
            return $field;
        }

        foreach ($urls as $url) {
            $typesFromApi = RestRequestHelper::get(trailingslashit($url) . 'taxonomies');

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

                $value            = trailingslashit($url) . ",{$type->slug}" . ",{$type->rest_base}";
                $labelParenthesis = trailingslashit($url) . $type->rest_base;
                $label            = "{$type->slug}: {$labelParenthesis}";
                $choices[$value]  = $label;
            }
        }

        if (!empty($choices)) {
            $field['choices'] = $choices;
        }

        return $field;
    }

    /**
     * Loads attachment source options for a given field.
     *
     * @param array $field The field to load attachment source options for.
     * @return array|null The modified field with attachment source options or null if an error occurred.
     */
    public function loadAttachmentSourceOptions($field): ?array
    {
        $choices = [];

        if (!function_exists('get_field')) {
            return $field;
        }

        $endpoints = get_field('api_resources_apis', 'options');

        if (!is_array($endpoints) || empty($endpoints)) {
            return $field;
        }

        $urls = array_map(fn ($row) => $row['url'], $endpoints);
        $urls = array_filter($urls, fn ($url) => filter_var($url, FILTER_VALIDATE_URL) !== false);

        if (empty($urls)) {
            return $field;
        }

        foreach ($urls as $url) {
            $typesFromApi = RestRequestHelper::get(trailingslashit($url) . 'types');

            if (is_wp_error($typesFromApi) || empty($typesFromApi)) {
                return null;
            }

            foreach ($typesFromApi as $type) {
                if (
                    !isset($type->slug) ||
                    $type->slug !== 'attachment' ||
                    !isset($type->_links) ||
                    !isset($type->_links->collection) ||
                    empty($type->_links->collection) ||
                    !isset($type->_links->collection[0]->href) ||
                    !filter_var($type->_links->collection[0]->href, FILTER_VALIDATE_URL)
                ) {
                    continue;
                }

                $value            = trailingslashit($url) . ",{$type->slug}" . ",{$type->rest_base}";
                $labelParenthesis = trailingslashit($url) . $type->rest_base;
                $label            = "{$type->slug}: {$labelParenthesis}";
                $choices[$value]  = $label;
            }
        }

        if (!empty($choices)) {
            $field['choices'] = $choices;
        }

        return $field;
    }

    /**
     * Loads attachment arguments for post types.
     *
     * @param array $field The field array.
     * @return array The modified field array.
     */
    public function loadAttachmentArgumentsPostTypes($field): array
    {

        $choices = [];

        if (!function_exists('get_field')) {
            return $field;
        }

        $postTypes = get_posts(['post_type' => self::POST_TYPE_NAME, 'posts_per_page' => -1]);

        if (!empty($postTypes)) {
            foreach ($postTypes as $postType) {
                $choices[$postType->ID] = $postType->post_title;
            }

            $field['choices'] = $choices;
        }

        return $field;
    }

    /**
     * Sets the post type resource post title from ACF.
     *
     * @param int $postId The ID of the post.
     * @return void
     */
    public function setPostTypeResourcePostTitleFromAcf($postId)
    {
        // Retrieve the post type arguments from ACF.
        $postTypeArguments = get_field('post_type_arguments', $postId);

        // Check if the post type arguments are empty or invalid.
        if (
            empty($postTypeArguments) ||
            !isset($postTypeArguments['post_type_key']) ||
            empty($postTypeArguments['post_type_key'])
        ) {
            return;
        }

        // Generate a sanitized post type name based on the post type key.
        $postTypeName = $postTypeArguments['post_type_key'];
        $postTypeName = sanitize_title(substr($postTypeName, 0, 19));

        // Update the post with the new post type name.
        wp_update_post([
            'ID'         => $postId,
            'post_title' => $postTypeName,
            'post_name'  => $postTypeName,
        ]);
    }

    /**
     * Sets the taxonomy resource post title from ACF.
     *
     * This function retrieves the taxonomy arguments from the Advanced Custom Fields (ACF) of a post
     * and uses it to update the post title and post name with the corresponding taxonomy key.
     *
     * @param int $postId The ID of the post.
     * @return void
     */
    public function setTaxonomyResourcePostTitleFromAcf($postId)
    {
        // Retrieve the taxonomy arguments from ACF
        $taxonomyArguments = get_field('taxonomy_arguments', $postId);

        // Check if the taxonomy arguments are empty or invalid
        if (
            empty($taxonomyArguments) ||
            !isset($taxonomyArguments['taxonomy_key']) ||
            empty($taxonomyArguments['taxonomy_key'])
        ) {
            return;
        }

        // Get the taxonomy key and sanitize it
        $taxonomyName = $taxonomyArguments['taxonomy_key'];
        $taxonomyName = sanitize_title(substr($taxonomyName, 0, 31));

        // Update the post title and post name with the taxonomy key
        wp_update_post([
            'ID'         => $postId,
            'post_title' => $taxonomyName,
            'post_name'  => $taxonomyName,
        ]);
    }

    /**
     * Sets the attachment resource post title from ACF.
     *
     * This method is responsible for setting the post title and post name of an attachment resource post
     * based on the selected post types from the ACF field 'attachment_arguments'.
     *
     * @param int $postId The ID of the attachment resource post.
     * @return void
     */
    public function setAttachmentResourcePostTitleFromAcf($postId)
    {
        if (get_post_type($postId) !== self::POST_TYPE_NAME) {
            return;
        }

        $attachmentArguments = get_field('attachment_arguments', $postId);

        if (
            empty($attachmentArguments) ||
            !isset($attachmentArguments['post_types']) ||
            empty($attachmentArguments['post_types'])
        ) {
            return;
        }

        $selectedPostTypes       = get_posts([
            'post_type'      => self::POST_TYPE_NAME,
            'post__in'       => $attachmentArguments['post_types'],
            'posts_per_page' => -1]);
        $selectedPostTypeNames   = array_map(fn ($post) => $post->post_title, $selectedPostTypes);
        $selectedPostTypesString = join(', ', $selectedPostTypeNames);
        $postTitle               = 'attachment for: ' . $selectedPostTypesString;
        $postName                = sanitize_title(substr($postTitle, 0, 19));

        wp_update_post([
            'ID'         => $postId,
            'post_title' => $postTitle,
            'post_name'  => $postName,

        ]);
    }

    /**
     * Sets the API meta data for a given post.
     *
     * @param int $postId The ID of the post.
     * @return void
     */
    public function setApiMeta($postId)
    {
        if (
            !is_string(get_post_type($postId)) ||
            get_post_type($postId) !== self::POST_TYPE_NAME ||
            !function_exists('get_field')
        ) {
            return;
        }

        if (get_field('type', $postId) ===  ResourceType::ATTACHMENT) {
            $source = get_field('attachment_source', $postId);
        } elseif (get_field('type', $postId) ===  ResourceType::POST_TYPE) {
            $source = get_field('post_type_source', $postId);
        } elseif (get_field('type', $postId) ===  ResourceType::TAXONOMY) {
            $source = get_field('taxonomy_source', $postId);
        } else {
            return;
        }

        if (!is_string($source) || empty($source)) {
            return;
        }

        $parts = explode(',', $source);

        if (sizeof($parts) !== 3) {
            return;
        }

        $url          = $parts[0];
        $originalName = $parts[1];
        $baseName     = $parts[2];

        update_post_meta($postId, 'api_resource_url', $url);
        update_post_meta($postId, 'api_resource_original_name', $originalName);
        update_post_meta($postId, 'api_resource_base_name', $baseName);
    }

    /**
     * Sanitizes the post type key before saving.
     *
     * @param string $value The post type key value.
     * @param int $postId The post ID.
     * @param array $field The field array.
     * @param string $originalValue The original post type key value.
     * @return string The sanitized post type key value.
     */
    public function sanitizePostTypeKeyBeforeSave($value, $postId, $field, $originalValue): string
    {
        return sanitize_title(substr($value, 0, 19));
    }

    /**
     * Sanitizes the taxonomy key before saving.
     *
     * @param string $value The value to be sanitized.
     * @param int $postId The ID of the post being saved.
     * @param array $field The field being saved.
     * @param string $originalValue The original value before sanitization.
     * @return string The sanitized taxonomy key.
     */
    public function sanitizeTaxonomyKeyBeforeSave($value, $postId, $field, $originalValue): string
    {
        return sanitize_title(substr($value, 0, 31));
    }
}
