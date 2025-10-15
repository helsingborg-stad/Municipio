<?php

namespace Municipio\Controller\ArchiveEvent;

use Municipio\Helper\EnsureArrayOf\EnsureArrayOf;
use Municipio\Schema\Event;
use Municipio\Schema\Place;

/**
 * Class GetEventPlaceName
 *
 * Provides utility to extract the place name from an Event object.
 */
class GetEventPlaceName
{
    /**
     * Retrieves the name of the first place associated with the given event.
     * Falls back to the address if the name is not available.
     *
     * @param Event $event The event object to extract the place name from.
     * @return string|null The name or address of the first place, or null if not available.
     */
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
