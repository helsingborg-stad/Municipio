<?php

namespace Municipio\Content\ResourceFromApi\Modifiers;

use Municipio\Content\ResourceFromApi\ResourceInterface;
use Municipio\Content\ResourceFromApi\ResourceRegistry\ResourceRegistryInterface;
use Municipio\Content\ResourceFromApi\ResourceType;
use Municipio\Helper\ResourceFromApiHelper;
use WP_Query;

/**
 * Class ModifiersHelper
 *
 * This class provides helper methods for modifiers.
 */
class ModifiersHelper implements ModifiersHelperInterface
{
    private ResourceRegistryInterface $resourceRegistry;
    private const TERM_QUERY_CACHE_GROUP_PREFIX = 'termQueryRemoteResourceResults-';

    /**
     * Constructs a new instance of the ModifiersHelper class.
     *
     * @param ResourceRegistryInterface $resourceRegistry The resource registry.
     */
    public function __construct(ResourceRegistryInterface $resourceRegistry)
    {
        $this->resourceRegistry = $resourceRegistry;
    }

    /**
     * Get the resource from the WP_Query object.
     *
     * @param WP_Query $query The WP_Query object.
     * @return object|null The resource object or null if not found.
     */
    public function getResourceFromQuery(WP_Query $query): ?object
    {
        if ($query->get('post__in') && !empty($query->get('post__in'))) {
            $ids = $query->get('post__in');

            if (self::containsIdFromResource($ids)) {
                return self::getResourceFromPostId($ids[0]);
            }
        }

        if ($query->get('post_type') && is_string($query->get('post_type'))) {
            $resources         = $this->resourceRegistry->getByType(ResourceType::POST_TYPE);
            $matchingResources = array_filter($resources, fn ($r) => $r->getName() === $query->get('post_type'));

            if (!empty($matchingResources)) {
                return reset($matchingResources);
            }
        }

        return null;
    }

    /**
     * Check if the given array of IDs contains IDs from a resource.
     *
     * @param array $ids The array of IDs.
     * @return bool True if the array contains IDs from a resource, false otherwise.
     */
    public function containsIdFromResource(array $ids): bool
    {
        foreach ($ids as $id) {
            if (ResourceFromApiHelper::isRemotePostID($id)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the resource from the given post ID.
     *
     * @param int $postId The post ID.
     * @return ResourceInterface|null The resource object or null if not found.
     */
    public function getResourceFromPostId($postId): ?ResourceInterface
    {
        if (!ResourceFromApiHelper::isRemotePostID((int)$postId)) {
            return null;
        }

        $resources = $this->resourceRegistry->getRegistry();

        foreach ($resources as $resource) {
            $haystack = (string)absint($postId);

            if (str_starts_with($haystack, (string)$resource->getResourceID())) {
                return $resource;
            }
        }

        return null;
    }

    /**
     * Prepares the query arguments for making a request to the API.
     *
     * @param array $queryArgs The original query arguments.
     * @param object $resource The resource object.
     * @return array The modified query arguments.
     */
    public function prepareQueryArgsForRequest(array $queryArgs, object $resource): array
    {
        $postIn = isset($queryArgs['post__in']) && is_array($queryArgs['post__in'])
            ? array_filter($queryArgs['post__in'], fn ($id) => !empty($id))
            : [];

        if (!empty($postIn)) {
            $queryArgs['post__in'] = array_map(
                fn ($id) => ResourceFromApiHelper::getRemoteId($id, $resource),
                $postIn
            );
        }

        if (isset($queryArgs['tax_query']) && is_array($queryArgs['tax_query']) && !empty($queryArgs['tax_query'])) {
            foreach ($queryArgs['tax_query'] as $key => $taxQuery) {
                if (isset($taxQuery['taxonomy']) && is_string($taxQuery['taxonomy']) && !empty($taxQuery['taxonomy'])) {
                    $queryArgs['tax_query'][$key]['taxonomy'] = self::possiblyConvertLocalTaxonomyToRemote(
                        $taxQuery['taxonomy']
                    );
                }

                if (isset($taxQuery['terms']) && is_array($taxQuery['terms']) && !empty($taxQuery['terms'])) {
                    $queryArgs['tax_query'][$key]['terms'] = array_map(
                        function ($id) {
                            if ($id > 0) {
                                return $id;
                            }
                            $taxResource = self::getResourceFromPostId($id);
                            return !empty($taxResource) ? ResourceFromApiHelper::getRemoteId($id, $taxResource) : $id;
                        },
                        $taxQuery['terms']
                    );
                }
            }
        }

        return $queryArgs;
    }

    /**
     * Convert a local taxonomy to its remote counterpart.
     *
     * @param string $taxonomy The local taxonomy.
     * @return string The remote taxonomy.
     */
    public function possiblyConvertLocalTaxonomyToRemote(string $taxonomy): string
    {
        $resources         = $this->resourceRegistry->getByType(ResourceType::TAXONOMY);
        $matchingResources = array_filter($resources, fn ($r) => $r->getName() === $taxonomy);

        if (!empty($matchingResources)) {
            return reset($matchingResources)->getOriginalName();
        }

        return $taxonomy;
    }

    /**
     * Get the term query result from cache.
     *
     * @param string $cacheKey The cache key.
     * @param string $taxonomy The taxonomy.
     * @return array|null The term query result from cache.
     */
    public function getTermQueryResultFromCache(string $cacheKey, string $taxonomy): ?array
    {
        $cacheGroup   = self::TERM_QUERY_CACHE_GROUP_PREFIX . $taxonomy;
        $foundInCache = wp_cache_get($cacheKey, $cacheGroup);

        if ($foundInCache) {
            return $foundInCache;
        }

        return null;
    }

    /**
     * Add the term query result to the cache.
     *
     * @param string $cacheKey The cache key.
     * @param array $collection The term query result collection.
     * @param string $taxonomy The taxonomy.
     * @return void
     */
    public function addTermQueryResultToCache(string $cacheKey, array $collection, string $taxonomy): void
    {
        $cacheGroup = self::TERM_QUERY_CACHE_GROUP_PREFIX . $taxonomy;
        wp_cache_add($cacheKey, $collection, $cacheGroup);
    }

    /**
     * Get the cache key for the term query.
     *
     * @param array $queryVars The query variables.
     * @return string The cache key.
     */
    public function getTermQueryCacheKey(array $queryVars): string
    {
        return md5(json_encode($queryVars));
    }
}
