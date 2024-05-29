<?php

namespace Municipio\ExternalContent\WpPostFactory;

use Municipio\ExternalContent\Sources\ISource;
use Municipio\ExternalContent\WpPostFactory\WpPostFactoryInterface;
use Spatie\SchemaOrg\BaseType;

class SchemaDataDecorator implements WpPostFactoryInterface
{
    public function __construct(private WpPostFactoryInterface $inner)
    {
    }

    public function create(BaseType $schemaObject, ISource $source): array
    {
        $post                             = $this->inner->create($schemaObject, $source);
        $post['meta_input']['schemaData'] = $schemaObject->toArray();

        return $post;
    }
}
