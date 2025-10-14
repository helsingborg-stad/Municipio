<?php

namespace Municipio\Controller\ArchiveEvent;

use Municipio\PostObject\PostObjectInterface;
use Municipio\Schema\Place;

class GetEventPlaceName
{
    public static function getEventPlaceName(PostObjectInterface $post): ?string
    {
        $locations     = $post->getSchemaProperty('location');
        $firstLocation = is_array($locations) ? reset($locations) : $locations;

        if (!is_a($firstLocation, Place::class)) {
            return null;
        }

        return $firstLocation->getProperty('name')
            ?: $firstLocation->getProperty('address')
            ?: null;
    }
}
