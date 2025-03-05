<?php

namespace Municipio\ExternalContent\WpPostArgsFromSchemaObject;

use Spatie\SchemaOrg\BaseType;

/**
 * Class SourceIdDecorator
 */
class SourceIdDecorator implements WpPostArgsFromSchemaObjectInterface
{
    /**
     * SourceIdDecorator constructor.
     *
     * @param string $sourceId
     * @param WpPostArgsFromSchemaObjectInterface $inner
     */
    public function __construct(private string $sourceId, private WpPostArgsFromSchemaObjectInterface $inner)
    {
    }

    /**
     * @inheritDoc
     */
    public function transform(BaseType $schemaObject): array
    {
        $post                           = $this->inner->transform($schemaObject);
        $post['meta_input']['sourceId'] = $this->sourceId;

        return $post;
    }
}
