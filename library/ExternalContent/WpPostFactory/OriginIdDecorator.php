<?php

namespace Municipio\ExternalContent\WpPostFactory;

use Municipio\ExternalContent\Sources\SourceInterface;
use Municipio\ExternalContent\WpPostFactory\WpPostFactoryInterface;
use Spatie\SchemaOrg\BaseType;

class OriginIdDecorator implements WpPostFactoryInterface
{
    public function __construct(private WpPostFactoryInterface $inner)
    {
    }

    public function create(BaseType $schemaObject, SourceInterface $source): array
    {
        $post                           = $this->inner->create($schemaObject, $source);
        $post['meta_input']['originId'] = $schemaObject['@id'];

        return $post;
    }
}
