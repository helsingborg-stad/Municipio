<?php

namespace Municipio\ExternalContent\WpPostArgsFromSchemaObject;

use Municipio\ExternalContent\Sources\SourceInterface;
use Municipio\ExternalContent\WpPostArgsFromSchemaObject\WpPostArgsFromSchemaObjectInterface;
use Spatie\SchemaOrg\BaseType;

class SchemaDataDecorator implements WpPostArgsFromSchemaObjectInterface
{
    public function __construct(private WpPostArgsFromSchemaObjectInterface $inner)
    {
    }

    public function create(BaseType $schemaObject, SourceInterface $source): array
    {
        $post                             = $this->inner->create($schemaObject, $source);
        $post['meta_input']['schemaData'] = $schemaObject->toArray();

        return $post;
    }
}
