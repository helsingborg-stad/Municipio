<?php

namespace Municipio\Controller\Archive\Mappers\GetPostsConfig;

use Municipio\PostsList\Config\GetPostsConfig\OrderDirection;
use Municipio\Controller\Archive\Mappers\MapperInterface;

/**
 * Map order from data
 */
class MapOrderFromData implements MapperInterface
{
    /**
     * Map order from data
     * Defaults to DESC (ArchiveDefaults::ORDER)
     *
     * @param array $data
     * @return OrderDirection
     */
    public function map(array $data): OrderDirection
    {
        return (isset($data['archiveProps']->orderDirection) && strtoupper($data['archiveProps']->orderDirection) === 'ASC')
            ? OrderDirection::ASC
            : OrderDirection::DESC; // ArchiveDefaults::ORDER = 'desc'
    }
}
