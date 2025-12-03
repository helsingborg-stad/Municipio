<?php

namespace Municipio\Controller\Archive\Mappers\AppearanceConfigMappers;

use Municipio\Controller\Archive\Mappers\MapperInterface;

/**
 * Mapper for post date source in appearance config
 */
class MapDateSource implements MapperInterface
{
    /**
     * Map post date source from data
     * @param array $data
     * @return string
     */
    public function map(array $data): string
    {
        return $data['archiveProps']->dateField ?? 'post_date';
    }
}
