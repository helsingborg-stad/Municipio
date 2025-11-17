<?php

namespace Municipio\Controller\Archive\Mappers\GetPostsConfig;

use Municipio\Controller\Archive\Mappers\MapperInterface;

class MapPostTypeFromData implements MapperInterface
{
    public function map(array $data): string
    {
        return !empty($data['postType']) ? $data['postType'] : 'page';
    }
}
