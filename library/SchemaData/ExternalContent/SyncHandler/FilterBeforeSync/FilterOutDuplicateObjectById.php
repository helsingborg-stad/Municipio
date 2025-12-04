<?php

namespace Municipio\SchemaData\ExternalContent\SyncHandler\FilterBeforeSync;

use Municipio\Schema\BaseType;

/**
 * Class FilterOutDuplicateObjectById
 */
class FilterOutDuplicateObjectById
{
    /**
     * Filter out duplicate objects from a collection based on their unique identifier.
     *
     * @param BaseType[] $schemaObjects
     */
    public function filter(array $schemaObjects): array
    {
        $ids = [];

        return array_filter($schemaObjects, function ($schemaObject) use (&$ids) {
            if (in_array($schemaObject->getProperty('@id'), $ids)) {
                return false;
            }

            $ids[] = $schemaObject->getProperty('@id');
            return true;
        });
    }
}
