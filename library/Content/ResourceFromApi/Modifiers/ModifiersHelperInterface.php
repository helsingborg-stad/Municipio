<?php

namespace Municipio\Content\ResourceFromApi\Modifiers;

use Municipio\Content\ResourceFromApi\ResourceInterface;
use Municipio\Content\ResourceFromApi\ResourceRegistry\ResourceRegistryInterface;
use WP_Query;

interface ModifiersHelperInterface
{
    /**
     * Constructor for the ModifiersHelperInterface.
     *
     * @param ResourceRegistryInterface $resourceRegistry The resource registry.
     */
    public function __construct(ResourceRegistryInterface $resourceRegistry);

    /**
     * Get the resource from the WP_Query object.
     *
     * @param WP_Query $query The WP_Query object.
     * @return object|null The resource object, or null if not found.
     */
    public function getResourceFromQuery(WP_Query $query): ?object;

    /**
     * Check if the given array of IDs contains an ID from the resource.
     *
     * @param array $ids The array of IDs.
     * @return bool True if an ID from the resource is found, false otherwise.
     */
    public function containsIdFromResource(array $ids): bool;

    /**
     * Get the resource from the given post ID.
     *
     * @param mixed $postId The post ID.
     * @return ResourceInterface|null The resource object, or null if not found.
     */
    public function getResourceFromPostId($postId): ?ResourceInterface;

    /**
     * Prepare the query arguments for the request.
     *
     * @param array $queryArgs The query arguments.
     * @param object $resource The resource object.
     * @return array The prepared query arguments.
     */
    public function prepareQueryArgsForRequest(array $queryArgs, object $resource): array;

    /**
     * Possibly convert a local taxonomy to a remote taxonomy.
     *
     * @param string $taxonomy The taxonomy to possibly convert.
     * @return string The converted taxonomy.
     */
    public function possiblyConvertLocalTaxonomyToRemote(string $taxonomy): string;

    /**
     * Get the term query result from the cache.
     *
     * @param string $cacheKey The cache key.
     * @param string $taxonomy The taxonomy.
     * @return array|null The term query result, or null if not found.
     */
    public function getTermQueryResultFromCache(string $cacheKey, string $taxonomy): ?array;

    /**
     * Add the term query result to the cache.
     *
     * @param string $cacheKey The cache key.
     * @param array $collection The collection of term query results.
     * @param string $taxonomy The taxonomy.
     * @return void
     */
    public function addTermQueryResultToCache(string $cacheKey, array $collection, string $taxonomy): void;

    /**
     * Get the cache key for the term query.
     *
     * @param array $queryVars The query variables.
     * @return string The cache key.
     */
    public function getTermQueryCacheKey(array $queryVars): string;
}
