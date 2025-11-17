<?php

namespace Municipio\Controller\Archive\Mappers\AppearanceConfigMappers;

class MapShouldDisplayFeaturedImage
{
    public function map(array $data): bool
    {
        $args = $data['archiveProps'] ?? (object) [];
        if (!is_object($args)) {
            $args = (object) [];
        }
        return isset($args->displayFeaturedImage) ? (bool) $args->displayFeaturedImage : false;
    }
}
