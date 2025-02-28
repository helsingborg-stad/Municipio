<?php

namespace Municipio\ExternalContent\WpPostArgsFromSchemaObject;

use Spatie\SchemaOrg\BaseType;

class DateDecorator implements WpPostArgsFromSchemaObjectInterface
{
    public function __construct(private WpPostArgsFromSchemaObjectInterface $inner)
    {
    }

    public function transform(BaseType $schemaObject): array
    {
        return array_merge(
            $this->inner->transform($schemaObject),
            [
                'post_date'     => $schemaObject['datePublished'] ?? null,
                'post_modified' => $schemaObject['dateModified'] ?? null,
            ]
        );
    }
}
