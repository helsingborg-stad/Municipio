<?php

namespace Municipio\Controller\Archive\Mappers;

class MapDateSourceFromData implements MapperInterface
{
    public function map(array $data): string
    {
        return $data['archiveProps']->dateField ?? 'post_date';
    }
}
