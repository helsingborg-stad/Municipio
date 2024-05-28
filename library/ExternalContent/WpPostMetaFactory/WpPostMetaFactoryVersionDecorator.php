<?php

namespace Municipio\ExternalContent\WpPostMetaFactory;

use Municipio\ExternalContent\Sources\ISource;
use Spatie\SchemaOrg\BaseType;

/**
 * Class WpPostMetaFactoryVersionDecorator
 */
class WpPostMetaFactoryVersionDecorator implements WpPostMetaFactoryInterface
{
    /**
     * WpPostMetaFactoryVersionDecorator constructor.
     *
     * @param WpPostMetaFactoryInterface $inner
     */
    public function __construct(private WpPostMetaFactoryInterface $inner)
    {
    }

    /**
     * @inheritDoc
     */
    public function create(BaseType $schemaObject, ISource $source): array
    {
        $meta = $this->inner->create($schemaObject, $source);

        if (isset($schemaObject['@version'])) {
            $meta['version'] = $schemaObject['@version'] ?? null;
        }

        return $meta;
    }
}
