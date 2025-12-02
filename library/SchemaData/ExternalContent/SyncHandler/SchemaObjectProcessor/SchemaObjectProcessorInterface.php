<?php

namespace Municipio\SchemaData\ExternalContent\SyncHandler\SchemaObjectProcessor;

use Municipio\Schema\BaseType;

/**
 * Interface for processing schema objects before they are used to create WordPress posts.
 */
interface SchemaObjectProcessorInterface
{
    /**
     * Process a schema object.
     *
     * @param BaseType $schemaObject
     * @return BaseType processed schema object
     */
    public function process(BaseType $schemaObject): BaseType;
}
