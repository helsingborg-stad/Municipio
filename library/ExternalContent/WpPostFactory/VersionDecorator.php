<?php

namespace Municipio\ExternalContent\WpPostFactory;

use Municipio\ExternalContent\Sources\SourceInterface;
use Spatie\SchemaOrg\BaseType;

/**
 * Class WpPostMetaFactoryVersionDecorator
 */
class VersionDecorator implements WpPostFactoryInterface
{
    /**
     * WpPostMetaFactoryVersionDecorator constructor.
     *
     * @param WpPostMetaFactoryInterface $inner
     */
    public function __construct(private WpPostFactoryInterface $inner)
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
