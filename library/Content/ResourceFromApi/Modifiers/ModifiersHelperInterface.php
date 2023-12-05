<?php

namespace Municipio\Content\ResourceFromApi\Modifiers;

use Municipio\Content\ResourceFromApi\ResourceInterface;
use Municipio\Content\ResourceFromApi\ResourceRegistryInterface;
use WP_Query;

interface ModifiersHelperInterface
{
    public function __construct(ResourceRegistryInterface $resourceRegistry);

    public function getResourceFromQuery(WP_Query $query): ?object;

    public function containsIdFromResource(array $ids): bool;

    public function getResourceFromPostId($postId): ?ResourceInterface;

    public function prepareQueryArgsForRequest(array $queryArgs, object $resource): array;

    public function possiblyConvertLocalTaxonomyToRemote(string $taxonomy): string;

    public function getTermQueryResultFromCache(string $cacheKey, string $taxonomy): ?array;

    public function addTermQueryResultToCache(string $cacheKey, array $collection, string $taxonomy): void;

    public function getTermQueryCacheKey(array $queryVars): string;
}
