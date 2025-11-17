<?php

namespace Municipio\Controller\Archive\Mappers\GetPostsConfig;

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
     * @return string The order by field, defaults to 'post_date' if not set
     */
    public function map(array $data): string
    {
        return $this->data['archiveProps']->dateField ?? 'post_date';
    }
}
