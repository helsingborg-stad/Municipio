<?php

namespace Municipio\Content;

use Municipio\Helper\RestRequestHelper;
use WP_Post;
use WP_Query;

class CustomPostTypesFromApi
{
    private array $postTypesFromApi = [];
    private array $postTypesWithParentPostTypes = [];

    public function __construct()
    {
        add_action('init', function () {
            $this->postTypesFromApi = $this->getPostTypesFromApi();
            $this->postTypesWithParentPostTypes = $this->getPostTypesWithParentPostTypes();
        }, 10);
    }

    private function getPostTypesFromApi(): array
    {
        $typeDefinitions = CustomPostType::getTypeDefinitions();
        $postTypesFromApi = array_filter(
            $typeDefinitions,
            fn ($typeDefinition) =>
            isset($typeDefinition['api_source_url']) &&
                !empty($typeDefinition['api_source_url']) &&
                filter_var($typeDefinition['api_source_url'], FILTER_VALIDATE_URL) !== false
        );

        return array_map(fn ($postType) => sanitize_title(substr($postType['post_type_name'], 0, 19)), $postTypesFromApi);
    }

    private function getPostTypesWithParentPostTypes(): array
    {

        $postTypesWithParentPostTypes = [];
        $typeDefinitions = CustomPostType::getTypeDefinitions();
        $matches = array_filter(
            $typeDefinitions,
            fn ($typeDefinition) =>
            isset($typeDefinition['parent_post_types']) && !empty($typeDefinition['parent_post_types'])
        );

        if (empty($matches)) {
            return [];
        }

        foreach ($matches as $match) {
            $postType = sanitize_title(substr($match['post_type_name'], 0, 19));
            $postTypesWithParentPostTypes[$postType] = $match['parent_post_types'];
        }

        return $postTypesWithParentPostTypes;
    }

    public function addHooks(): void
    {
        add_filter('post_type_link', [$this, 'modifyPostTypeLink'], 10, 2);
        add_filter('posts_results', [$this, 'modifyPostsResults'], 10, 2);
        add_filter('default_post_metadata', [$this, 'modifyDefaultPostMetadata'], 10, 5);
        add_filter('Municipio/Breadcrumbs/Items', [$this, 'modifyBreadcrumbsItems'], 10, 3);
        add_filter('Municipio/Content/RestApiPostToWpPost', [$this, 'addParentToPostWithParentPostType'], 10, 3);

        add_action('pre_get_posts', [$this, 'preventSuppressFiltersOnWpQuery'], 200, 1);
        add_action('pre_get_posts', [$this, 'preventCacheOnPreGetPosts'], 200, 1);
        add_action('init', [$this, 'addRewriteRulesForPostTypesWithParentPostTypes'], 10, 0);
        
        add_action('init', [$this, 'addPostType']);
        add_action('acf/init', [$this, 'addOptionsPage']);
        
        add_filter('acf/load_field/name=post_type_source', [$this, 'loadPostTypeSourceOptions']);
        add_filter('acf/load_field/name=taxonomy_source', [$this, 'loadTaxonomySourceOptions']);

        add_action('acf/save_post', [$this, 'setResourcePostTitleFromAcf'], 10);
        add_filter('acf/update_value/name=post_type_key', [$this, 'sanitizePostTypeKeyBeforeSave'], 10, 4);
    }

    public function modifyPostTypeLink(string $postLink, WP_Post $post)
    {
        $postType = get_post_type($post);

        if (isset($this->postTypesWithParentPostTypes[$postType])) {

            $parentPost = get_post($post->post_parent);
            $parentPostType = $parentPost->post_type;

            if (in_array($parentPostType, $this->postTypesWithParentPostTypes[$postType])) {
                $parentPostTypeObject = get_post_type_object($parentPostType);
                $postTypeObject = get_post_type_object($postType);
                $rewriteSlug = $postTypeObject->rewrite['slug'];
                $parentRewriteSlug = $parentPostTypeObject->rewrite['slug'];
                $postLink = str_replace($rewriteSlug, $parentRewriteSlug, $postLink);
            }
        }

        return $postLink;
    }

    public function modifyBreadcrumbsItems(?array $pageData, $queriedObject, $queriedObjectData): ?array
    {
        if (is_null($pageData) || !is_a($queriedObject, 'WP_Post')) {
            return $pageData;
        }

        // if post type in entity registry
        if (
            !isset($this->postTypesWithParentPostTypes[$queriedObject->post_type]) ||
            !is_array($queriedObject->post_type) ||
            empty($queriedObject->post_parent)
        ) {
            return $pageData;
        }

        foreach ($this->postTypesWithParentPostTypes[$queriedObject->post_type] as $parentPostType) {

            $parentPosts = get_posts(['post__in' => [$queriedObject->post_parent], 'post_type' => $parentPostType, 'suppress_filters' => false]);

            if (!empty($parentPosts)) {
                break;
            }
        }

        if (empty($parentPosts)) {
            return $pageData;
        }

        // Insert new element before the last one in $pageData.
        array_splice($pageData, -1, 0, [
            [
                'label'   => $parentPosts[0]->post_title,
                'href'    => get_post_permalink($parentPosts[0]),
                'current' => false
            ],
        ]);

        return $pageData;
    }

    public function modifyPostsResults(array $posts, WP_Query $query): array
    {
        if (
            !$query->get('post_type') ||
            !in_array($query->get('post_type'), $this->postTypesFromApi)
        ) {
            return $posts;
        }

        if ($query->is_single()) {
            $posts = CustomPostTypeFromApi::getSingle($query->get('name'), $query->get('post_type'));
        } else {
            $posts = CustomPostTypeFromApi::getCollection($query, $query->get('post_type'));
            $headers = CustomPostTypeFromApi::getCollectionHeaders($query, $query->get('post_type'));
            $query->found_posts = $headers['x-wp-total'] ?? count($posts);
            $query->max_num_pages = $headers['x-wp-totalpages'] ?? 1;
        }

        return is_array($posts) ? $posts : [$posts];
    }

    public function modifyDefaultPostMetadata($value, $objectId, $metaKey, $single, $metaType)
    {
        $postType = get_post_type($objectId);

        if (!in_array($postType, $this->postTypesFromApi)) {
            return $value;
        }

        return CustomPostTypeFromApi::getMeta($objectId, $metaKey, $single, $metaType, $postType) ?? $value;
    }

    public function addParentToPostWithParentPostType(WP_Post $wpPost, object $restApiPost, string $postType)
    {
        if (!isset($this->postTypesWithParentPostTypes[$postType])) {
            return $wpPost;
        }

        // TODO: replace with real parent from API.
        $wpPost->post_parent = $restApiPost->acf->parent_school ?? 0;

        return $wpPost;
    }

    public function preventSuppressFiltersOnWpQuery(WP_Query $query): void
    {
        if (!in_array($query->get('post_type'), $this->postTypesFromApi)) {
            return;
        }

        $query->query['suppress_filters'] = false;
    }

    public function preventCacheOnPreGetPosts(WP_Query $query): void
    {
        if (!in_array($query->get('post_type'), $this->postTypesFromApi)) {
            return;
        }

        $query->set('update_post_meta_cache', false);
        $query->set('update_post_term_cache', false);
    }

    public function addRewriteRulesForPostTypesWithParentPostTypes(): void
    {
        foreach ($this->postTypesWithParentPostTypes as $postType => $parentPostTypes) {

            if (!post_type_exists($postType)) {
                return;
            }

            foreach ($parentPostTypes as $parentPostType) {

                if (!post_type_exists($parentPostType)) {
                    continue;
                }

                $parentPostTypeObject = get_post_type_object($parentPostType);
                $rewriteSlug = $parentPostTypeObject->rewrite['slug'];

                add_rewrite_rule(
                    $rewriteSlug . '/(.*)/(.*)',
                    'index.php?post_type=' . $postType . '&name=$matches[2]',
                    'top'
                );
            }
        }
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

    function loadPostTypeSourceOptions( $field ) {

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

        foreach($urls as $url) {
            
            $typesFromApi = RestRequestHelper::getFromApi(trailingslashit($url) . 'types');

            if (is_wp_error($typesFromApi) || empty($typesFromApi)) {
                return null;
            }

            foreach($typesFromApi as $type) {
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

                $value = trailingslashit($type->_links->collection[0]->href) . $type->rest_base;
                $label = "{$type->slug}: {$value}";
                $choices[$value] = $label;
            }
        }

        if( !empty($choices) ) {
            $field['choices'] = $choices;
        }

        return $field;
        
    }
    
    function loadTaxonomySourceOptions( $field ) {

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

        foreach($urls as $url) {
            
            $typesFromApi = RestRequestHelper::getFromApi(trailingslashit($url) . 'taxonomies');

            if (is_wp_error($typesFromApi) || empty($typesFromApi)) {
                return null;
            }

            foreach($typesFromApi as $type) {
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

                $value = trailingslashit($type->_links->collection[0]->href) . $type->rest_base;
                $label = "{$type->slug}: {$value}";
                $choices[$value] = $label;
            }
        }

        if( !empty($choices) ) {
            $field['choices'] = $choices;
        }

        return $field;
        
    }

    public function setResourcePostTitleFromAcf(int $postId)
    {
        $postTypeArguments = get_field('post_type_argruments', $postId);

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
        ]);
    }

    public function sanitizePostTypeKeyBeforeSave($value, $postId, $field, $originalValue)
    {
        return sanitize_title(substr($value, 0, 19));
    }

    public function populatePostTypsesField(array $field) {
        return $field;
    }
}
