<?php

namespace Municipio\Controller\Archive\Mappers\FilterConfigMappers;

use Municipio\Controller\Archive\Mappers\MapperInterface;

/**
 * Map text search enabled
 */
class MapTextSearchEnabled implements MapperInterface
{
    /**
     * Map text search enabled
     *
     * @param array $data
     * @return mixed
     */
    public function map(array $data): mixed
    {
        if (!is_object($data['archiveProps'])) {
            $data['archiveProps'] = (object) [];
        }

        return (bool) in_array(
            'text_search',
            isset($data['archiveProps']->enabledFilters) && is_array($data['archiveProps']->enabledFilters)
                ? $data['archiveProps']->enabledFilters
                : []
        );
    }
}
