<?php

namespace Municipio\Controller\Archive\Mappers\GetPostsConfig;

use Municipio\Controller\Archive\Mappers\MapperInterface;

/**
 * Map date source from data
 */
class MapDateSourceFromData implements MapperInterface
{
    /**
     * Map date source from data
     *
     * @param array $data
     * @return string
     */
    public function map(array $data): string
    {
        return $data['archiveProps']->dateField ?? 'post_date';
    }
}
