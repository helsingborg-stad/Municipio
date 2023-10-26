<?php

namespace Municipio\Content;

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
            'post-type-from-api',
            [
                'labels' => [
                    'name' => __('Custom post type from API', 'municipio'),
                    'singular_name' => __('Custom post type from API', 'municipio'),
                ],
                'show_ui' => true,
                'public' => true,
                'has_archive' => false,
                'show_in_rest' => false,
                'supports' => [],
                'taxonomies' => [],
            ]
        );
    }
}
