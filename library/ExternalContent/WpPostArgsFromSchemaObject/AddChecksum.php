<?php

namespace Municipio\ExternalContent\WpPostArgsFromSchemaObject;

use Spatie\SchemaOrg\BaseType;

/**
 * Class AddChecksum
 */
class AddChecksum implements WpPostArgsFromSchemaObjectInterface
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
    public function create(BaseType $schemaObject): array
    {
        $postArgs = $this->inner->create($schemaObject);
        $checksum = md5(json_encode($this->inner->create($schemaObject)));

        if (!isset($postArgs['meta_input'])) {
            $postArgs['meta_input'] = [];
        }

        $postArgs['meta_input']['checksum'] = $checksum;
        return $postArgs;
    }
}
