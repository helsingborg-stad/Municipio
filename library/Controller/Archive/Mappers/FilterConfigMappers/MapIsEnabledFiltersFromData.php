<?php

namespace Municipio\Controller\Archive\Mappers\FilterConfigMappers;

use Municipio\Controller\Archive\Mappers\MapperInterface;
use WpService\Contracts\ApplyFilters;

class MapIsEnabledFiltersFromData implements MapperInterface
{
    public function __construct(private ApplyFilters $wpService)
    {
    }

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
