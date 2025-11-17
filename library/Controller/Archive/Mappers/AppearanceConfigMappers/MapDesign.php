<?php

namespace Municipio\Controller\Archive\Mappers\AppearanceConfigMappers;

use Municipio\PostsList\Config\AppearanceConfig\PostDesign;

class MapDesign
{
    public function map(array $data): PostDesign
    {
        return match ($data['archiveProps']->style ?? 'cards') {
            'cards' => PostDesign::CARD,
            'compressed' => PostDesign::COMPRESSED,
            'collection' => PostDesign::COLLECTION,
            'grid' => PostDesign::BLOCK,
            'newsitem' => PostDesign::NEWSITEM,
            'schema' => PostDesign::SCHEMA,
            'list' => PostDesign::TABLE,
            default => PostDesign::CARD,
        };
    }
}
