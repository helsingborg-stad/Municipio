<?php

namespace Modularity\Module\Posts\Helper\GetPosts\PostTypesFromSchemaType;

class NullPostTypesFromSchemaTypeResolver implements PostTypesFromSchemaTypeResolverInterface
{
    public function __construct()
    {
    }

    /**
     * @inheritDoc
     */
    public function resolve(string $schemaType): array {
        return [];
    }
}