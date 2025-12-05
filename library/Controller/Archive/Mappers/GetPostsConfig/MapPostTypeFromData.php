<?php

namespace Municipio\Controller\Archive\Mappers\GetPostsConfig;

use Municipio\Controller\Archive\Mappers\MapperInterface;

/**
 * Map post type from data
 */
class MapPostTypeFromData implements MapperInterface
{
    /**
     * Map post type from data
     *
     * @param array $data
     * @return string
     */
    public function map(array $data): string
    {
        return !empty($data['postType']) ? $data['postType'] : 'page';
    }
}
