<?php

namespace Municipio\SchemaData\ExternalContent\SyncHandler\FilterBeforeSync;

use Municipio\SchemaData\ExternalContent\SyncHandler\SyncHandler;
use Municipio\HooksRegistrar\Hookable;
use Municipio\Schema\BaseType;

/**
 * Class FilterOutDuplicateObjectById
 */
class FilterOutDuplicateObjectById implements Hookable
{
    /**
     * @inheritDoc
     */
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
