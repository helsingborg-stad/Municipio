<?php

namespace Municipio\Controller\Archive\Mappers\FilterConfigMappers;

use Municipio\Controller\Archive\Mappers\MapperInterface;

class MapTextSearchEnabled implements MapperInterface
{
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
