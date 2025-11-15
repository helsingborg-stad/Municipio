<?php

namespace Municipio\Controller\Archive\Mappers;

class MapOrderByFromData implements MapperInterface
{
    public function map(array $data): string
    {
        return $this->data['archiveProps']->dateField ?? 'post_date';
    }
}
