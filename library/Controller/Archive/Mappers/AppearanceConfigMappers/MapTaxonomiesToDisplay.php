<?php

namespace Municipio\Controller\Archive\Mappers\AppearanceConfigMappers;

/**
 * Maps taxonomies to display from the provided data
 */
class MapTaxonomiesToDisplay
{
    /**
     * Maps taxonomies to display
     *
     * @param array $data Archive configuration data
     * @return array List of taxonomies to display
     */
    public function map(array $data): array
    {
        $props = $data['archiveProps'] ?? (object) [];
        return $props->taxonomiesToDisplay ?? [];
    }
}
