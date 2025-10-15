<?php

namespace Modularity\Module\Posts\Helper\GetPosts\PostTypesFromSchemaType;

use Municipio\SchemaData\Helper\GetSchemaType;

class PostTypesFromSchemaTypeResolver implements PostTypesFromSchemaTypeResolverInterface
{
    public function __construct(private PostTypesFromSchemaTypeResolverInterface $nextResolver = new NullPostTypesFromSchemaTypeResolver())
    {
    }

    /**
     * @inheritDoc
     */
    public function resolve(string $schemaType): array
    {
        $postTypes = GetSchemaType::getPostTypesFromSchemaType($schemaType);

        if (is_array($postTypes)) {
            return $postTypes;
        }

        return $this->nextResolver->resolve($schemaType);
    }
}
