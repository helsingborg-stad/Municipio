<?php

namespace Municipio\Content\ResourceFromApi;

interface ResourceRequestInterface
{
    public function getCollection(?array $queryArgs = null): array;
    public function getCollectionHeaders(?array $queryArgs = null): array;
    public function getSingle($id): ?object;
    public function getMeta(int $id, string $metaKey, bool $single = true);
}
