<?php

namespace Municipio\ExternalContent\WpPostArgsFromSchemaObject;

use Municipio\ExternalContent\WpPostArgsFromSchemaObject\WpPostArgsFromSchemaObjectInterface;
use Spatie\SchemaOrg\BaseType;

class OriginIdDecorator implements WpPostArgsFromSchemaObjectInterface
{
    public function __construct(private WpPostArgsFromSchemaObjectInterface $inner)
    {
    }

    public function create(BaseType $schemaObject): array
    {
        $post                           = $this->inner->create($schemaObject);
        $post['meta_input']['originId'] = $schemaObject['@id'];

        return $post;
    }
}
