<?php

namespace Municipio\Controller\Archive\Mappers\GetPostsConfig;

use Municipio\Controller\Archive\Mappers\MapperInterface;

class MapIsFacettingFromData implements MapperInterface
{
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
