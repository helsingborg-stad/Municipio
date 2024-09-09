<?php

namespace Municipio\ExternalContent\WpPostArgsFromSchemaObject;

use Municipio\ExternalContent\Sources\SourceInterface;
use Spatie\SchemaOrg\BaseType;

/**
 * Class WpPostMetaFactoryVersionDecorator
 */
class VersionDecorator implements WpPostArgsFromSchemaObjectInterface
{
    /**
     * WpPostMetaFactoryVersionDecorator constructor.
     *
     * @param WpPostMetaFactoryInterface $inner
     */
    public function __construct(private WpPostArgsFromSchemaObjectInterface $inner)
    {
    }

    /**
     * @inheritDoc
     */
    public function create(BaseType $schemaObject, SourceInterface $source): array
    {
        $post = $this->inner->create($schemaObject, $source);

        if (isset($schemaObject['@version'])) {
            $post['meta_input']['version'] = $schemaObject['@version'] ?? null;
        }

        return $post;
    }
}
