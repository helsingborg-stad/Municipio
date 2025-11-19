<?php

namespace Municipio\Controller\Archive\Mappers\GetPostsConfig;

use Municipio\Controller\Archive\Mappers\MapperInterface;

/**
 * Map isFacetting from data
 */
class MapIsFacettingFromData implements MapperInterface
{
    /**
     * Map isFacetting from data
     *
     * @param array $data
     * @return bool
     */
    public function map(array $data): bool
    {
        if (!is_object($data['archiveProps'])) {
            $data['archiveProps'] = (object) [];
        }

        if (!isset($data['archiveProps']->filterType) || is_null($data['archiveProps']->filterType)) {
            $data['archiveProps']->filterType = false;
        }

        return (bool) $data['archiveProps']->filterType;
    }
}
