<?php

namespace Municipio\ExternalContent\WpPostFactory;

use Municipio\ExternalContent\Sources\SourceInterface;
use Spatie\SchemaOrg\BaseType;

class SourceIdDecorator implements WpPostFactoryInterface
{
    public function __construct(private WpPostFactoryInterface $inner)
    {
    }

    public function create(BaseType $schemaObject, SourceInterface $source): array
    {
        $post                           = $this->inner->create($schemaObject, $source);
        $post['meta_input']['sourceId'] = $source->getId();

        return $post;
    }
}
