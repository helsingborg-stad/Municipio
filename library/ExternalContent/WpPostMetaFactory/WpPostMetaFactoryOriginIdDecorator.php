<?php

namespace Municipio\ExternalContent\WpPostMetaFactory;

use Spatie\SchemaOrg\BaseType;

class WpPostMetaFactoryOriginIdDecorator implements WpPostMetaFactoryInterface
{
    public function __construct(private WpPostMetaFactoryInterface $inner)
    {
    }

    public function create(BaseType $schemaObject): array
    {
        $meta             = $this->inner->create($schemaObject);
        $meta['originId'] = $schemaObject['@id'];

        return $meta;
    }
}
