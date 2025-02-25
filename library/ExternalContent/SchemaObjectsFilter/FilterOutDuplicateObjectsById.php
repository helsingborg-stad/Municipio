<?php

namespace Municipio\ExternalContent\SchemaObjectsFilter;

/**
 * Class FilterOutDuplicateObjectsById
 *
 * This class is responsible for filtering out duplicate objects from a collection based on their unique identifier.
 * It ensures that only unique objects remain in the collection by comparing their IDs.
 */
class FilterOutDuplicateObjectsById implements SchemaObjectsFilterInterface
{
    /**
     * @inheritDoc
     */
    public function applyFilter(array $schemaObjects): array
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
