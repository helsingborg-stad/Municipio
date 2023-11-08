<?php

namespace Municipio\Content\ResourceFromApi\PostType;

use Municipio\Content\ResourceFromApi\QueriesModifierInterface;
use Municipio\Content\ResourceFromApi\ResourceRegistryInterface;
use Municipio\Helper\RestRequestHelper;
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
        add_filter('post_type_link', [$this, 'modifyPostTypeLink'], 10, 2);
        add_filter('posts_results', [$this, 'modifyPostsResults'], 10, 2);
        add_filter('default_post_metadata', [$this, 'modifyDefaultPostMetadata'], 100, 5);
        add_filter('acf/pre_load_value', [$this, 'preLoadAcfValue'], 10, 3);
        add_filter('Municipio/Breadcrumbs/Items', [$this, 'modifyBreadcrumbsItems'], 10, 3);
        add_action('pre_get_posts', [$this, 'preventSuppressFiltersOnWpQuery'], 200, 1);
        add_action('pre_get_posts', [$this, 'preventCacheOnPreGetPosts'], 200, 1);
        add_action('Municipio/Content/ResourceFromApi/ConvertRestApiPostToWPPost', [$this, 'addParentToPost'], 10, 3);
    }

    public function preLoadAcfValue($value, $postId, $field)
    {
        if (!isset($field['name']) || !$this->isRemotePostId($postId)) {
            return $value;
        }

        $registeredPostType = $this->getResourceFromPostId($postId);
        $postId = (int)str_replace((string)$registeredPostType->resourceID, '', (string)absint($postId));

        return PostTypeResourceRequest::getMeta($postId, $field['name'], $registeredPostType) ?? $value;
    }

    public function modifyBreadcrumbsItems(?array $pageData, $queriedObject, $queriedObjectData): ?array
    {
        if (is_null($pageData) || !is_a($queriedObject, 'WP_Post')) {
            return $pageData;
        }

        $registeredPostType = $this->resourceRegistry->getRegisteredPostType($queriedObject->post_type);

        if (empty($registeredPostType)) {
            return $pageData;
        }

        if ($queriedObject->post_parent === 0) {
            return $pageData;
        }

        $parentPosts = get_posts(['post__in' => [$queriedObject->post_parent], 'suppress_filters' => false]);

        if (empty($parentPosts)) {
            return $pageData;
        }

        array_splice($pageData, -1, 0, [
            [
                'label'   => $parentPosts[0]->post_title,
                'href'    => get_post_permalink($parentPosts[0]),
                'current' => false
            ],
        ]);

        return $pageData;
    }

    public function modifyPostTypeLink(string $postLink, WP_Post $post)
    {
        if ($post->post_parent === 0) {
            return $postLink;
        }

        $registeredPostType = $this->resourceRegistry->getRegisteredPostType($post->post_type);
        $postTypeObject = get_post_type_object($post->post_type);

        if (empty($registeredPostType) || empty($postTypeObject)) {
            return $postLink;
        }

        if (!str_starts_with($postTypeObject->rewrite['slug'], '/%parentPost%')) {
            return $postLink;
        }

        $path     = trim(parse_url(get_post_permalink($post->post_parent))['path'], '/');
        $postLink = str_replace('%parentPost%', $path, $postLink);

        return $postLink;
    }

    public function modifyPostsResults(array $posts, WP_Query $query): array
    {
        $registeredPostType = $this->getResourceFromQuery($query);

        if (empty($registeredPostType)) {
            return $posts;
        }

        if ($query->is_single()) {
            $posts = PostTypeResourceRequest::getSingle($query->get('name'), $registeredPostType);
        } else {
            $queryVars = $this->prepareQueryArgsForRequest($query->query_vars, $registeredPostType);
            $queryVars = 
            $posts = PostTypeResourceRequest::getCollection($registeredPostType, $queryVars);
            $headers = PostTypeResourceRequest::getCollectionHeaders($registeredPostType, $queryVars);
            $query->found_posts = $headers['x-wp-total'] ?? count($posts);
            $query->max_num_pages = $headers['x-wp-totalpages'] ?? 1;
        }

        return is_array($posts) ? $posts : [$posts];
    }

    private function getResourceFromQuery(WP_Query $query): ?object
    {
        if ($query->get('post__in') && !empty($query->get('post__in'))) {

            $ids = $query->get('post__in');

            if ($this->containsIdFromResource($ids)) {
                return $this->getResourceFromPostId($ids[0]);
            }
        }

        if ($query->get('post_type') && is_string($query->get('post_type'))) {
            return $this->resourceRegistry->getRegisteredPostType($query->get('post_type'));
        }

        return null;
    }

    private function containsIdFromResource(array $ids): bool
    {
        foreach ($ids as $id) {
            if ($this->isRemotePostId((int)$id)) {
                return true;
            }
        }

        return false;
    }

    public function modifyDefaultPostMetadata($value, int $objectId, $metaKey, $single, $metaType)
    {
        $registeredPostType = $this->getResourceFromPostId($objectId);

        if (is_null($registeredPostType)) {
            return $value;
        }

        $objectId = $this->prepareIdForRequest($objectId, $registeredPostType);

        return PostTypeResourceRequest::getMeta($objectId, $metaKey, $registeredPostType, $single) ?? $value;
    }

    private function prepareQueryArgsForRequest(array $queryArgs, object $resource): array
    {
        $postIn = isset($queryArgs['post__in']) && is_array($queryArgs['post__in']) ? array_filter($queryArgs['post__in'], fn($id) => !empty($id)) : [];
        if (!empty($postIn)) {
            $queryArgs['post__in'] = array_map(
                fn ($id) => $this->prepareIdForRequest($id, $resource),
                $postIn
            );
        }

        if( isset($queryArgs['tax_query']) && is_array($queryArgs['tax_query']) && !empty($queryArgs['tax_query']) ) {
            foreach($queryArgs['tax_query'] as $key => $taxQuery) {
                
                if( isset($taxQuery['taxonomy']) && is_string($taxQuery['taxonomy']) && !empty($taxQuery['taxonomy']) ) {
                    $queryArgs['tax_query'][$key]['taxonomy'] = $this->possiblyConvertLocalTaxonomyToRemote($taxQuery['taxonomy']);
                }
                
                if( isset($taxQuery['terms']) && is_array($taxQuery['terms']) && !empty($taxQuery['terms']) ) {
                    $queryArgs['tax_query'][$key]['terms'] = array_map(
                        function ($id) {    
                            $taxResource = $this->getResourceFromPostId($id);
                            return !empty($taxResource) ? $this->prepareIdForRequest($id, $taxResource) : $id;
                        },
                        $taxQuery['terms']
                    );
                }
            }
        }

        return $queryArgs;
    }

    private function possiblyConvertLocalTaxonomyToRemote (string $taxonomy): string
    {
        $localTaxonomyResource = $this->resourceRegistry->getRegisteredTaxonomy($taxonomy);

        if (
            !empty($localTaxonomyResource) &&
            isset($localTaxonomyResource->originalName) &&
            !empty($localTaxonomyResource->originalName)
        ) {
            return $localTaxonomyResource->originalName;
        }

        return $taxonomy;
    }

    private function prepareIdForRequest(int $id, object $resource): int
    {
        return (int)str_replace((string)$resource->resourceID, '', (string)absint($id));
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

    private function getResourceFromPostId($postId): ?object
    {

        if (!$this->isRemotePostId((int)$postId)) {
            return null;
        }

        $registeredPostTypes = $this->resourceRegistry->getRegisteredPostTypes();
        $registeredTaxonomies = $this->resourceRegistry->getRegisteredTaxonomies();

        $registeredResources = array_merge($registeredPostTypes, $registeredTaxonomies);

        if (empty($registeredResources)) {
            return null;
        }

        foreach ($registeredResources as $registeredResource) {
            $needle = (string)$registeredResource->resourceID;
            $haystack = (string)absint($postId);

            if (str_starts_with($haystack, $needle)) {
                return $registeredResource;
            }
        }

        return null;
    }

    private function isRemotePostId($id): bool
    {
        return is_numeric($id) && (int)$id < 0;
    }

    public function addParentToPost($wpPost, $restApiPost, $localPostType)
    {

        if ($wpPost->post_parent === 0) {
            return $wpPost;
        }

        if (!isset($restApiPost->_links) || !isset($restApiPost->_links->up)) {
            return $wpPost;
        }

        $parentUrl = $restApiPost->_links->up[0]->href;

        if (empty($parentUrl)) {
            return $wpPost;
        }

        // Get parent post from API and convert to WP_Post. Then use the id from that WP_Post as the parent id for $wpPost.
        $parentPostFromApi = RestRequestHelper::getFromApi($parentUrl);
        $parentId = $parentPostFromApi->id;
        $parentResource = null;

        if (!isset($parentPostFromApi->_links) || !isset($parentPostFromApi->_links->collection)) {
            return $wpPost;
        }

        $parentCollectionUrl = $parentPostFromApi->_links->collection[0]->href;

        $resources = $this->resourceRegistry->getRegisteredPostTypes();

        foreach ($resources as $resource) {
            if ($resource->collectionUrl === $parentCollectionUrl) {
                $parentResource = $resource;
                break;
            }
        }

        if (empty($parentResource)) {
            return $wpPost;
        }

        $parentPost = PostTypeResourceRequest::getSingle($parentId, $parentResource);

        if (empty($parentPost)) {
            return $wpPost;
        }

        $localParentId = $parentPost->ID;
        $wpPost->post_parent = $localParentId;

        return $wpPost;
    }
}
