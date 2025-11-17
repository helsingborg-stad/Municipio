<?php

namespace Municipio\Controller\Archive\Mappers\AppearanceConfigMappers;

class MapNumberOfColumns
{
    public function map(array $data): int
    {
        return $data['archiveProps']->numberOfColumns ?? 1;
    }
}
