<?php

namespace Municipio\Controller\Archive\Mappers\AppearanceConfigMappers;

use Municipio\Controller\Archive\ArchiveDefaults;
use Municipio\Controller\Archive\ArchivePropertyResolver;

/**
 * Map if featured image should be displayed in archive items
 */
class MapShouldDisplayFeaturedImage
{
    /**
     * Map data
     * Supports both camelCase (displayFeaturedImage) and snake_case (featured_image)
     * Defaults to ArchiveDefaults::DISPLAY_FEATURED_IMAGE
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
        return ArchivePropertyResolver::resolveBool(
            $args,
            'displayFeaturedImage',
            'featured_image'
        );
    }
}
