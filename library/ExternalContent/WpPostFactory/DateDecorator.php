<?php

namespace Municipio\ExternalContent\WpPostFactory;

use Municipio\ExternalContent\Sources\SourceInterface;
use Spatie\SchemaOrg\BaseType;

class DateDecorator implements WpPostFactoryInterface
{
    public function __construct(private WpPostFactoryInterface $inner)
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
