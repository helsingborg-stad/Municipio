<?php

namespace Municipio\Controller\Archive\Mappers\AppearanceConfigMappers;

/**
 * Maps the number of columns for archive appearance configuration
 */
class MapNumberOfColumns
{
    /**
     * Maps the number of columns from the provided data
     *
     * @param array $data The input data containing archive properties
     * @return int The number of columns, defaults to 1 if not set
     */
    public function map(array $data): int
    {
        return $data['archiveProps']->numberOfColumns ?? 1;
    }
}
