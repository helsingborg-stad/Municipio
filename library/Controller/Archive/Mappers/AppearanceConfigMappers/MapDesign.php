<?php

namespace Municipio\Controller\Archive\Mappers\AppearanceConfigMappers;

use Municipio\PostsList\Config\AppearanceConfig\PostDesign;

/**
 * Mapper for post design in appearance config
 */
class MapDesign
{
    /**
     * Map post design from data
     * @param array $data
     * @return PostDesign
     */
    public function map(array $data): PostDesign
    {
        return match ($data['archiveProps']->style ?? null) {
            'cards' => PostDesign::CARD,
            'collection' => PostDesign::COLLECTION,
            'compressed' => PostDesign::COMPRESSED,
            'grid' => PostDesign::BLOCK,
            'list' => PostDesign::TABLE,
            'newsitem' => PostDesign::NEWSITEM,
            'schema' => PostDesign::SCHEMA,
            default => PostDesign::CARD,
        };
    }
}
