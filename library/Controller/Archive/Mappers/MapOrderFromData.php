<?php

namespace Municipio\Controller\Archive\Mappers;

use Municipio\PostsList\Config\GetPostsConfig\OrderDirection;

class MapOrderFromData implements MapperInterface
{
    public function map(array $data): OrderDirection
    {
        return (isset($data['archiveProps']->orderDirection) && strtoupper($data['archiveProps']->orderDirection) === 'ASC')
            ? OrderDirection::ASC
            : OrderDirection::DESC;
    }
}
