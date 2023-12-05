<?php

namespace Municipio\Content\ResourceFromApi\Modifiers;

use Municipio\Content\ResourceFromApi\ResourceInterface;
use Municipio\Content\ResourceFromApi\ResourceRegistryInterface;
use Municipio\Content\ResourceFromApi\ResourceType;
use Municipio\Helper\RemotePosts;
use WP_Query;

class ModifiersHelper {

    private static ResourceRegistryInterface $resourceRegistry;
    private const TERM_QUERY_CACHE_GROUP_PREFIX = 'termQueryRemoteResourceResults-';

    public function __construct(ResourceRegistryInterface $resourceRegistry)
    {
        self::$resourceRegistry = $resourceRegistry;
    }
    
    public static function getResourceFromQuery(WP_Query $query): ?object
    {
        if ($query->get('post__in') && !empty($query->get('post__in'))) {

            $ids = $query->get('post__in');

            if (self::containsIdFromResource($ids)) {
                return self::getResourceFromPostId($ids[0]);
            }
        }

        if ($query->get('post_type') && is_string($query->get('post_type'))) {
            $resources = self::$resourceRegistry->getByType(ResourceType::POST_TYPE);
            $matchingResources = array_filter($resources, fn($r) => $r->getName() === $query->get('post_type'));
            
            if( !empty($matchingResources) ) {
                return reset($matchingResources);
            }
        }

        return null;
    }

    public static function containsIdFromResource(array $ids): bool
    {
        foreach ($ids as $id) {
            
            if (RemotePosts::isRemotePostID($id)) {
                return true;
            }
        }

        return false;
    }

    public static function getResourceFromPostId($postId): ?ResourceInterface
    {
        if (!RemotePosts::isRemotePostID((int)$postId)) {
            return null;
        }

        $resources = self::$resourceRegistry->getRegistry();

        foreach ($resources as $resource) {
            $haystack = (string)absint($postId);

            if (str_starts_with($haystack, (string)$resource->getResourceID())) {
                return $resource;
            }
        }

        return null;
    }

    public static function prepareQueryArgsForRequest(array $queryArgs, object $resource): array
    {
        $postIn = isset($queryArgs['post__in']) && is_array($queryArgs['post__in']) ? array_filter($queryArgs['post__in'], fn($id) => !empty($id)) : [];
        if (!empty($postIn)) {
            $queryArgs['post__in'] = array_map(
                fn ($id) => RemotePosts::getRemoteId($id, $resource),
                $postIn
            );
        }

        if( isset($queryArgs['tax_query']) && is_array($queryArgs['tax_query']) && !empty($queryArgs['tax_query']) ) {
            foreach($queryArgs['tax_query'] as $key => $taxQuery) {
                
                if( isset($taxQuery['taxonomy']) && is_string($taxQuery['taxonomy']) && !empty($taxQuery['taxonomy']) ) {
                    $queryArgs['tax_query'][$key]['taxonomy'] = self::possiblyConvertLocalTaxonomyToRemote($taxQuery['taxonomy']);
                }
                
                if( isset($taxQuery['terms']) && is_array($taxQuery['terms']) && !empty($taxQuery['terms']) ) {
                    $queryArgs['tax_query'][$key]['terms'] = array_map(
                        function ($id) {    
                            if( $id > 0 ) {
                                return $id;
                            }
                            $taxResource = self::getResourceFromPostId($id);
                            return !empty($taxResource) ? RemotePosts::getRemoteId($id, $taxResource) : $id;
                        },
                        $taxQuery['terms']
                    );
                }
            }
        }

        return $queryArgs;
    }

    public static function possiblyConvertLocalTaxonomyToRemote(string $taxonomy): string
    {
        $resources = self::$resourceRegistry->getByType(ResourceType::TAXONOMY);
        $matchingResources = array_filter($resources, fn ($r) => $r->getName() === $taxonomy);

        if (!empty($matchingResources)) {
            return reset($matchingResources)->getOriginalName();
        }

        return $taxonomy;
    }

    public static function getTermQueryResultFromCache(string $cacheKey, string $taxonomy): ?array
    {
        $cacheGroup = self::TERM_QUERY_CACHE_GROUP_PREFIX . $taxonomy;
        $foundInCache = wp_cache_get($cacheKey, $cacheGroup);

        if ($foundInCache) {
            return $foundInCache;
        }

        return null;
    }

    public static function addTermQueryResultToCache(string $cacheKey, array $collection, string $taxonomy): void
    {
        $cacheGroup = self::TERM_QUERY_CACHE_GROUP_PREFIX . $taxonomy;
        wp_cache_add($cacheKey, $collection, $cacheGroup);
    }

    public static function getTermQueryCacheKey(array $queryVars):string {
        return md5(json_encode($queryVars));
    }
}