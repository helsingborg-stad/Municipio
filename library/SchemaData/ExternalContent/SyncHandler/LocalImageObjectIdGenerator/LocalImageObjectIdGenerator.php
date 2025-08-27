<?php

namespace Municipio\SchemaData\ExternalContent\SyncHandler\LocalImageObjectIdGenerator;

use Municipio\Schema\BaseType;
use Municipio\Schema\ImageObject;

class LocalImageObjectIdGenerator implements LocalImageObjectIdGeneratorInterface
{
    public function generateId(BaseType $schemaObject, ImageObject $imageObject): string
    {
        return $schemaObject->getProperty('@id') . '-' . $imageObject->getProperty('sameAs');
    }
}
