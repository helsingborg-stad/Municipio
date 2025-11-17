<?php

namespace Municipio\Controller\Archive\Mappers\AppearanceConfigMappers;

class MapTaxonomiesToDisplay
{
    public function map(array $data): array
    {
        $props = $data['archiveProps'] ?? (object) [];
        return $props->taxonomiesToDisplay ?? [];
    }
}
