<?php

namespace Municipio\Controller\Archive\Mappers\GetPostsConfig;

use Municipio\Controller\Archive\Mappers\MapperInterface;

class MapOrderByFromData implements MapperInterface
{
    public function map(array $data): string
    {
        return $this->data['archiveProps']->dateField ?? 'post_date';
    }
}
