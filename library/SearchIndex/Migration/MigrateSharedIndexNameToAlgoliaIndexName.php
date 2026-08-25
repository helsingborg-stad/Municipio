<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Migration;

use AcfService\Contracts\GetField;
use AcfService\Contracts\UpdateField;

/**
 * Moves the former shared index name into the Algolia-specific setting.
 */
class MigrateSharedIndexNameToAlgoliaIndexName
{
    public function __construct(private GetField&UpdateField $acfService) {}

    /**
     * Preserve a configured shared index name without overwriting the new setting.
     */
    public function migrate(): void
    {
        $algoliaIndexName = $this->acfService->getField('search_index_algolia_index_name', 'option');

        if ($this->hasValue($algoliaIndexName)) {
            return;
        }

        $sharedIndexName = $this->acfService->getField('search_index_name', 'option');

        if ($this->hasValue($sharedIndexName)) {
            $this->acfService->updateField('search_index_algolia_index_name', $sharedIndexName, 'option');
        }
    }

    /**
     * Determine whether an ACF setting contains a usable value.
     */
    private function hasValue(mixed $value): bool
    {
        return is_string($value) && $value !== '';
    }
}