<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\SearchPage;

use AcfService\Contracts\GetField;

/**
 * Reads SearchPage subfeature settings.
 */
class SearchPageConfig
{
    public function __construct(private GetField $acfService) {}

    /**
     * Determine whether the interactive SearchPage subfeature is enabled.
     */
    public function isEnabled(): bool
    {
        return (bool) $this->acfService->getField('search_index_search_page_enabled', 'option');
    }
}