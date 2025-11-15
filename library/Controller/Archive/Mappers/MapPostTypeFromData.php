<?php

namespace Municipio\Controller\Archive\Mappers;

class MapPostTypeFromData implements MapperInterface
{
    public function map(array $data): string
    {
        return !empty($data['postType']) ? $data['postType'] : 'page';
    }
}
