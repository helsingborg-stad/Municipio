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

        if (isset($post['meta_input']['schemaData']['id'])) {
            // Remove id that might come from Typesense to avoid conflicts
            unset($post['meta_input']['schemaData']['id']);
        }

        return $post;
    }
}
