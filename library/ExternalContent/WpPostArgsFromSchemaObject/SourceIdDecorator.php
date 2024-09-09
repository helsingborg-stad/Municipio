<?php

namespace Municipio\ExternalContent\WpPostArgsFromSchemaObject;

use Municipio\ExternalContent\Sources\SourceInterface;
use Spatie\SchemaOrg\BaseType;

class SourceIdDecorator implements WpPostArgsFromSchemaObjectInterface
{
    public function __construct(private WpPostArgsFromSchemaObjectInterface $inner)
    {
    }

    public function create(BaseType $schemaObject, SourceInterface $source): array
    {
        $post                           = $this->inner->create($schemaObject, $source);
        $post['meta_input']['sourceId'] = $source->getId();

        return $post;
    }
}
