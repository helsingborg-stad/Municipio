<?php

namespace Municipio\Controller\Archive\Mappers\FilterConfigMappers;

use Municipio\Controller\Archive\Mappers\MapperInterface;

/**
 * Maps whether the date range filter is enabled
 */
class MapDateFilterEnabled implements MapperInterface
{
    /**
     * Maps whether the date range filter is enabled
     *
     * @param array $data Archive configuration data
     * @return bool True if date range filter is enabled, false otherwise
     */
    public function map(array $data): mixed
    {
        if (!is_object($data['archiveProps'])) {
            $data['archiveProps'] = (object) [];
        }

        return (bool) in_array(
            'date_range',
            isset($data['archiveProps']->enabledFilters) && is_array($data['archiveProps']->enabledFilters)
                ? $data['archiveProps']->enabledFilters
                : []
        );
    }
}
