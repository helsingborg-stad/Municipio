<?php

namespace Municipio\Controller\Archive\Mappers\AppearanceConfigMappers;

class MapPostPropertiesToDisplay
{
    public function map(array $data): array
    {
        $props = $data['archiveProps'] ?? (object) [];
        return $props->postPropertiesToDisplay ?? [];
    }
}
