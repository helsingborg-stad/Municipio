<?php

namespace Municipio\Controller\Archive\Mappers\GetPostsConfig;

use Municipio\Controller\Archive\Mappers\MapperInterface;

class MapDateSourceFromData implements MapperInterface
{
    public function map(array $data): string
    {
        return $data['archiveProps']->dateField ?? 'post_date';
    }
}
