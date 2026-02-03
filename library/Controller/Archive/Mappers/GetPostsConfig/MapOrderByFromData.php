<?php

namespace Municipio\Controller\Archive\Mappers\GetPostsConfig;

use Municipio\Controller\Archive\ArchiveDefaults;
use Municipio\Controller\Archive\Mappers\MapperInterface;

/**
 * Maps the order by field from the provided data
 */
class MapOrderByFromData implements MapperInterface
{
    /**
     * Map order by field from data
     *
     * @param array $data The input data containing archive properties
     * @return string The order by field, defaults to ArchiveDefaults::ORDER_BY if not set
     */
    public function map(array $data): string
    {
        return $data['archiveProps']->orderBy ?? ArchiveDefaults::ORDER_BY;
    }
}
