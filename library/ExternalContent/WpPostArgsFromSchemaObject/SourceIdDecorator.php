<?php

namespace Municipio\ExternalContent\WpPostArgsFromSchemaObject;

use Spatie\SchemaOrg\BaseType;

class SourceIdDecorator implements WpPostArgsFromSchemaObjectInterface
{
    public function __construct(private string $sourceId, private WpPostArgsFromSchemaObjectInterface $inner)
    {
    }

    public function create(BaseType $schemaObject): array
    {
        $post                           = $this->inner->create($schemaObject);
        $post['meta_input']['sourceId'] = $this->sourceId;

        return $post;
    }
}
