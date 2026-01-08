<?php

namespace Municipio\SchemaData\ExternalContent\SyncHandler\MetaDataFromSchema\Mappers;

use Generator;
use Municipio\Schema\BaseType;
use Municipio\SchemaData\ExternalContent\SyncHandler\MetaDataFromSchema\MetaDataItemInterface;

interface MetaDataItemMapperInterface
{
    /**
     * Map schema to meta data items
     *
     * @param BaseType $schema
     * @return Generator<MetaDataItemInterface>
     */
    public function map(BaseType $schema): Generator;
}
