<?php

namespace Municipio\Content\ResourceFromApi;

use WP_Post;
use WP_Query;

class PostTypeQueriesModifier implements QueriesModifierInterface
{
    private ResourceRegistryInterface $resourceRegistry;

    public function __construct(ResourceRegistryInterface $resourceRegistry)
    {
        $this->resourceRegistry = $resourceRegistry;
    }

    public function addHooks(): void
    {
        // add_filter('post_type_link', [$this, 'modifyPostTypeLink'], 10, 2);
        add_filter('posts_results', [$this, 'modifyPostsResults'], 10, 2);
        add_filter('default_post_metadata', [$this, 'modifyDefaultPostMetadata'], 100, 5);
        add_filter( 'acf/pre_load_value', [$this, 'preLoadAcfValue'], 10, 3 );
        // add_filter('Municipio/Breadcrumbs/Items', [$this, 'modifyBreadcrumbsItems'], 10, 3);
        // add_filter('Municipio/Content/RestApiPostToWpPost', [$this, 'addParentToPostWithParentPostType'], 10, 3);
        add_action('pre_get_posts', [$this, 'preventSuppressFiltersOnWpQuery'], 200, 1);
        add_action('pre_get_posts', [$this, 'preventCacheOnPreGetPosts'], 200, 1);
        // add_action('init', [$this, 'addRewriteRulesForPostTypesWithParentPostTypes'], 10, 0);
    }

    public function preLoadAcfValue($value, $postId, $field) {

        $registeredPostType = $this->getResourceFromPostId($postId);

        if (is_null($registeredPostType)) {
            return $value;
        }

        $postId = (int)str_replace((string)$registeredPostType->resourceID, '', (string)absint($postId));

        if( !isset($field['name']) ) {
            return $value;
        }

        return PostTypeResourceRequest::getMeta($postId, $field['name'], $registeredPostType) ?? $value;
    }

    public function modifyPostTypeLink(string $postLink, WP_Post $post)
    {
        if (!is_a($post, WP_Post::class)) {
            return $postLink;
        }

        $registeredPostType = $this->getResourceFromPostId($post->ID);


        if (empty($registeredPostType)) {
            return $postLink;
        }

        $postFromApi = PostTypeResourceRequest::getSingle($post->post_name, $registeredPostType);

        if( $postFromApi ){}

        $postType = get_post_type($post);
        return $postLink;
    }

    public function modifyPostsResults(array $posts, WP_Query $query): array
    {
        if (!$query->get('post_type') || !is_string($query->get('post_type'))) {
            return $posts;
        }

        $registeredPostType = $this->resourceRegistry->getRegisteredPostType($query->get('post_type'));

        if (empty($registeredPostType)) {
            return $posts;
        }

        if ($query->is_single()) {
            $posts = PostTypeResourceRequest::getSingle($query->get('name'), $registeredPostType);
        } else {
            $posts = PostTypeResourceRequest::getCollection($registeredPostType, $query->query_vars);
            $headers = PostTypeResourceRequest::getCollectionHeaders($registeredPostType, $query->query_vars);
            $query->found_posts = $headers['x-wp-total'] ?? count($posts);
            $query->max_num_pages = $headers['x-wp-totalpages'] ?? 1;
        }

        return is_array($posts) ? $posts : [$posts];
    }

    public function modifyDefaultPostMetadata($value, int $objectId, $metaKey, $single, $metaType)
    {
        $registeredPostType = $this->getResourceFromPostId($objectId);

        if (is_null($registeredPostType)) {
            return $value;
        }

        $objectId = (int)str_replace((string)$registeredPostType->resourceID, '', (string)absint($objectId));

        return PostTypeResourceRequest::getMeta($objectId, $metaKey, $registeredPostType, $single) ?? $value;
    }

    public function preventSuppressFiltersOnWpQuery(WP_Query $query): void
    {
        if (!$query->get('post_type') || !is_string($query->get('post_type'))) {
            return;
        }

        $registeredPostType = $this->resourceRegistry->getRegisteredPostType($query->get('post_type'));

        if (empty($registeredPostType)) {
            return;
        }

        $query->query['suppress_filters'] = false;
    }

    public function preventCacheOnPreGetPosts(WP_Query $query): void
    {
        if (!$query->get('post_type') || !is_string($query->get('post_type'))) {
            return;
        }

        $registeredPostType = $this->resourceRegistry->getRegisteredPostType($query->get('post_type'));

        if (empty($registeredPostType)) {
            return;
        }

        $query->set('update_post_meta_cache', false);
        $query->set('update_post_term_cache', false);
    }

    private function getResourceFromPostId($postId):?object {

        if( $postId > -1 ) {
            return null;
        }

        $registeredPostTypes = $this->resourceRegistry->getRegisteredPostTypes();
        
        if(empty($registeredPostTypes)) {
            return null;
        }

        foreach($registeredPostTypes as $registeredPostType) {
            $needle = (string)$registeredPostType->resourceID;
            $haystack = (string)absint($postId);

            if( str_starts_with($haystack, $needle) ) {
                return $registeredPostType;
            }
        }

        return null;
    }
}
