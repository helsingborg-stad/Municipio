<?php

namespace Municipio\ExternalContent\WpPostMetaFactory;

use Municipio\ExternalContent\Sources\ISource;
use Spatie\SchemaOrg\BaseType;

class WpPostMetaFactorySourceIdDecorator implements WpPostMetaFactoryInterface
{
    public function __construct(private WpPostMetaFactoryInterface $inner)
    {
    }

    public function create(BaseType $schemaObject, ISource $source): array
    {
        $meta             = $this->inner->create($schemaObject, $source);
        $meta['sourceId'] = $source->getId();

        return $meta;
    }
}
