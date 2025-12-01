<?php

namespace Municipio\Controller\Archive\Mappers\AppearanceConfigMappers;

/**
 * Maps post properties to display from archive props
 */
class MapPostPropertiesToDisplay
{
    /**
     * Map post properties to display
     *
     * @param array $data
     * @return array
     */
    public function map(array $data): array
    {
        $props = $data['archiveProps'] ?? (object) [];
        return $props->postPropertiesToDisplay ?? [];
    }
}
