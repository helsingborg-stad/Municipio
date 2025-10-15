<?php

namespace Modularity\Module\Posts\Helper\GetPosts\PostTypesFromSchemaType;

interface PostTypesFromSchemaTypeResolverInterface
{
    /**
     * Resolve post types from a schema type.
     *
     * @param string $schemaType The schema type to resolve.
     * @return string[] The resolved post types.
     */
    public function resolve(string $schemaType): array;
}