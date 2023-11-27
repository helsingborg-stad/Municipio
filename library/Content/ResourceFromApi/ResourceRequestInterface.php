<?php

namespace Municipio\Content\ResourceFromApi;

interface ResourceRequestInterface
{
    public static function getCollection(ResourceInterface $resource, ?array $queryArgs = null): array;
    public static function getSingle($id, ResourceInterface $resource): ?object;
    public static function getMeta(int $id, string $metaKey, ResourceInterface $resource, bool $single = true);
}