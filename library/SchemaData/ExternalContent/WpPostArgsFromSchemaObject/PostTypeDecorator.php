<?php

namespace Municipio\SchemaData\ExternalContent\WpPostArgsFromSchemaObject;

use Municipio\SchemaData\ExternalContent\WpPostArgsFromSchemaObject\WpPostArgsFromSchemaObjectInterface;
use Municipio\Schema\BaseType;

/**
 * Decorator for adding post type to post args
 */
class PostTypeDecorator implements WpPostArgsFromSchemaObjectInterface
{
    /**
     * Constructor
     *
     * @param string $postType
     * @param WpPostArgsFromSchemaObjectInterface $inner
     */
    public function __construct(private string $postType, private WpPostArgsFromSchemaObjectInterface $inner)
    {
    }

    /**
     * @inheritDoc
     */
    public function transform(BaseType $schemaObject): array
    {
        return [...$this->inner->transform($schemaObject), 'post_type' => $this->postType];
    }
}
