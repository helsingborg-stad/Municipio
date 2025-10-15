<?php

namespace Municipio\Controller\ArchiveEvent;

use Municipio\Helper\EnsureArrayOf\EnsureArrayOf;
use Municipio\Schema\Event;
use Municipio\Schema\Place;

class GetEventPlaceName
{
    public static function getEventPlaceName(Event $event): ?string
    {
        $locations = EnsureArrayOf::ensureArrayOf($event->getProperty('location'), Place::class);

        if (empty($locations)) {
            return null;
        }

        $firstLocation = reset($locations);

        return $firstLocation->getProperty('name')
            ?: $firstLocation->getProperty('address')
            ?: null;
    }
}
