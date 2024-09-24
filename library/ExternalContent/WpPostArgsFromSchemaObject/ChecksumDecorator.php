<?php

namespace Municipio\ExternalContent\WpPostArgsFromSchemaObject;

use Municipio\ExternalContent\Sources\SourceInterface;
use Spatie\SchemaOrg\BaseType;

/**
 * Class ChecksumDecorator
 */
class ChecksumDecorator implements WpPostArgsFromSchemaObjectInterface
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
        $postArgs = $this->inner->create($schemaObject, $source);
        $checksum = sha1(json_encode($this->inner->create($schemaObject, $source)));

        if (!isset($postArgs['meta_input'])) {
            $postArgs['meta_input'] = [];
        }

        $postArgs['meta_input']['checksum'] = $checksum;
        return $postArgs;
    }
}
