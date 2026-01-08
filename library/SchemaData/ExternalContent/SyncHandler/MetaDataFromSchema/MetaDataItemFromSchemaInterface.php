<?php

namespace Municipio\SchemaData\ExternalContent\SyncHandler\MetaDataFromSchema;

use Municipio\Schema\BaseType;

interface MetaDataItemFromSchemaInterface {
    public function getMetaDataItem(BaseType $schema): MetaDataItemInterface;
}