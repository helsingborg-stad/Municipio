<?php

namespace Municipio\ExternalContent\WpPostArgsFromSchemaObject;

use Municipio\ExternalContent\WpPostArgsFromSchemaObject\WpPostArgsFromSchemaObjectInterface;
use Spatie\SchemaOrg\BaseType;

/**
 * Class OriginIdDecorator
 */
class OriginIdDecorator implements WpPostArgsFromSchemaObjectInterface
{
    /**
     * OriginIdDecorator constructor.
     *
     * @param WpPostArgsFromSchemaObjectInterface $inner
     */
    public function __construct(private WpPostArgsFromSchemaObjectInterface $inner)
    {
    }

    /**
     * @inheritDoc
     */
    public function transform(BaseType $schemaObject): array
    {
        $post                           = $this->inner->transform($schemaObject);
        $post['meta_input']['originId'] = $schemaObject['@id'];

        return $post;
    }
}
