<?php

namespace Municipio\Controller\Archive\Mappers;

interface MapperInterface
{
    /**
     * Map data to a specific format
     *
     * @param array $data
     * @return mixed
     */
    public function map(array $data): mixed;
}
