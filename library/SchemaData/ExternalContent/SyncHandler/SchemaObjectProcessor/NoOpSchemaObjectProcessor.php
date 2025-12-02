<?php

namespace Municipio\SchemaData\ExternalContent\SyncHandler\SchemaObjectProcessor;

use Municipio\Schema\BaseType;

/**
 * Example processor that does nothing (for demonstration).
 */
class NoOpSchemaObjectProcessor implements SchemaObjectProcessorInterface
{
    /**
     * @inheritdoc
     */
    public function process(BaseType $schemaObject): BaseType
    {
        // No changes applied
        return $schemaObject;
    }
}
