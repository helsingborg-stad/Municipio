<?php

namespace Municipio\SchemaData\ExternalContent\SyncHandler\LocalImageObjectIdGenerator;

use Municipio\Schema\BaseType;
use Municipio\Schema\ImageObject;

/**
 * Generates a unique ID for a local image object based on the schema object ID and image URL.
 */
class LocalImageObjectIdGenerator implements LocalImageObjectIdGeneratorInterface
{
    /**
     * @inheritDoc
     */
    public function generateId(BaseType $schemaObject, ImageObject $imageObject): string
    {
        return $schemaObject->getProperty('@id') . '-' . $imageObject->getProperty('sameAs');
    }
}
