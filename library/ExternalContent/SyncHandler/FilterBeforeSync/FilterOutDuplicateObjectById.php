<?php

namespace Municipio\ExternalContent\SyncHandler\FilterBeforeSync;

use Municipio\ExternalContent\SyncHandler\SyncHandler;
use Municipio\HooksRegistrar\Hookable;
use Spatie\SchemaOrg\BaseType;

class FilterOutDuplicateObjectById implements Hookable
{
    public function addHooks(): void
    {
        add_filter(SyncHandler::FILTER_BEFORE, [$this, 'filter']);
    }

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
