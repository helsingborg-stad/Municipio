<?php

namespace Municipio\Controller\Archive\Mappers\FilterConfigMappers;

use Municipio\Controller\Archive\Mappers\MapperInterface;
use WpService\Contracts\ApplyFilters;

/**
 * Maps whether any filters are enabled from the provided data
 */
class MapIsEnabledFiltersFromData implements MapperInterface
{
    /**
     * Constructor
     *
     * @param ApplyFilters $wpService
     */
    public function __construct(private ApplyFilters $wpService)
    {
    }

    /**
     * Maps whether any filters are enabled
     *
     * @param array $data Archive configuration data
     * @return bool True if any filters are enabled, false otherwise
     */
    public function map(array $data): bool
    {
        $enabledFilters = false;

        if (!is_object($data['archiveProps'])) {
            $data['archiveProps'] = (object) [];
        }

        $arrayWithoutEmptyValues = isset($data['archiveProps']->enabledFilters)
            ? array_filter($data['archiveProps']->enabledFilters, fn($element) => !empty($element))
            : [];

        if (!empty($arrayWithoutEmptyValues)) {
            $enabledFilters = $data['archiveProps']->enabledFilters;
        }

        $enabledFilters = $this->wpService->applyFilters('Municipio/Archive/showFilter', $enabledFilters, $data['archiveProps']);

        return is_array($enabledFilters) && !empty($enabledFilters) ? true : (bool) $enabledFilters;
    }
}
