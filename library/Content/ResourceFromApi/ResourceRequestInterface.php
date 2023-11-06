<?php

namespace Municipio\Content\ResourceFromApi;

interface ResourceRequestInterface
{
    public static function getCollection(object $resource, ?array $queryArgs = null): array;
    public static function getSingle($id, object $resource): ?object;
    public static function getMeta(int $id, string $metaKey, object $resource, bool $single = true);
}