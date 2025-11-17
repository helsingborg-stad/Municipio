<?php

namespace Municipio\Controller\Archive\Mappers\AppearanceConfigMappers;

class MapShouldDisplayReadingTime
{
    public function map(array $data): bool
    {
        $args = $data['archiveProps'] ?? (object) [];
        if (!is_object($args)) {
            $args = (object) [];
        }
        return isset($args->readingTime) ? (bool) $args->readingTime : false;
    }
}
