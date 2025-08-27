<?php

namespace Municipio\SchemaData\ExternalContent\SyncHandler\LocalImageObjectIdGenerator;

use Municipio\Schema\BaseType;
use Municipio\Schema\ImageObject;

interface LocalImageObjectIdGeneratorInterface
{
    /**
     * Generates a unique ID for a local image object.
     */
    public function generateId(BaseType $schemaObject, ImageObject $imageObject): string;
}
