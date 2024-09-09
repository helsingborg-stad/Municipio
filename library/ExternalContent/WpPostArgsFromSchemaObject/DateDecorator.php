<?php

namespace Municipio\ExternalContent\WpPostArgsFromSchemaObject;

use Municipio\ExternalContent\Sources\SourceInterface;
use Spatie\SchemaOrg\BaseType;

class DateDecorator implements WpPostArgsFromSchemaObjectInterface
{
    public function __construct(private WpPostArgsFromSchemaObjectInterface $inner)
    {
    }

    public function create(BaseType $schemaObject, SourceInterface $source): array
    {
        return array_merge(
            $this->inner->create($schemaObject, $source),
            [
                'post_date'     => $schemaObject['datePublished'] ?? null,
                'post_modified' => $schemaObject['dateModified'] ?? null,
            ]
        );
    }
}
