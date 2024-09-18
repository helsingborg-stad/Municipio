<?php

namespace Municipio\ExternalContent\WpPostArgsFromSchemaObject;

use Municipio\ExternalContent\Sources\SourceInterface;
use Spatie\SchemaOrg\BaseType;

class WpPostFactory implements WpPostArgsFromSchemaObjectInterface
{
    public function create(BaseType $schemaObject, SourceInterface $source): array
    {
        return [
            'post_title'   => html_entity_decode($schemaObject['name'] ?? ''),
            'post_content' => html_entity_decode($schemaObject['description'] ?? ''),
            'post_status'  => 'publish',
            'post_type'    => $source->getPostType(),
            'meta_input'   => []
        ];
    }
}
