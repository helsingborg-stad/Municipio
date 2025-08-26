<?php

namespace Municipio\SchemaData\ExternalContent\WpPostArgsFromSchemaObject;

use Municipio\Schema\BaseType;

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
    public function transform(BaseType $schemaObject): array
    {
        $postArgs = $this->inner->transform($schemaObject);
        $checksum = md5(json_encode($this->inner->transform($schemaObject)));

        if (!isset($postArgs['meta_input'])) {
            $postArgs['meta_input'] = [];
        }

        $postArgs['meta_input']['checksum'] = $checksum;
        return $postArgs;
    }
}
