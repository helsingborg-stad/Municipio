<?php

namespace Municipio\Content\ResourceFromApi;

interface ResourceRequestInterface
{
    /**
     * Returns the collection of the resource.
     */
    public function getCollection(?array $queryArgs = null): array;

    /**
     * Returns the collection URL of the resource.
     */
    public function getCollectionHeaders(?array $queryArgs = null): array;

    /**
     * Returns the single of the resource.
     */
    public function getSingle($id): ?object;

    /**
     * Returns the meta of the resource.
     */
    public function getMeta(int $id, string $metaKey, bool $single = true);
}
