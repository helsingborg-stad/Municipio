<?php

namespace Municipio\Controller\Archive\Mappers\AppearanceConfigMappers;

/**
 * Map if featured image should be displayed in archive items
 */
class MapShouldDisplayFeaturedImage
{
    /**
     * Map data
     *
     * @param array $data
     * @return bool
     */
    public function map(array $data): bool
    {
        $args = $data['archiveProps'] ?? (object) [];
        if (!is_object($args)) {
            $args = (object) [];
        }
        return isset($args->displayFeaturedImage) ? (bool) $args->displayFeaturedImage : false;
    }
}
