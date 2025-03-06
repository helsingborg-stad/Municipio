<?php

namespace Municipio\ExternalContent\WpPostArgsFromSchemaObject;

use Municipio\ExternalContent\WpPostArgsFromSchemaObject\WpPostArgsFromSchemaObjectInterface;
use Spatie\SchemaOrg\BaseType;

/**
 * Class SchemaDataDecorator
 *
 * This class decorates the schema data by adding it to the post meta input.
 */
class SchemaDataDecorator implements WpPostArgsFromSchemaObjectInterface
{
    /**
     * SchemaDataDecorator constructor.
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
        $post                             = $this->inner->transform($schemaObject);
        $post['meta_input']['schemaData'] = $schemaObject->toArray();

        if (isset($post['meta_input']['schemaData']['id'])) {
            // Remove id that might come from Typesense to avoid conflicts
            unset($post['meta_input']['schemaData']['id']);
        }

        return $post;
    }
}
