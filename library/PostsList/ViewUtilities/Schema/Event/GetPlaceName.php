<?php

namespace Municipio\PostsList\ViewUtilities\Schema\Event;

use Municipio\Helper\EnsureArrayOf\EnsureArrayOf;
use Municipio\PostObject\PostObjectInterface;
use Municipio\PostsList\ViewUtilities\ViewUtilityInterface;
use Municipio\Schema\Event;
use Municipio\Schema\Place;

/**
 * Class GetPlaceName
 *
 * Provides utility to extract the place name from an Event object.
 */
class GetPlaceName implements ViewUtilityInterface
{
    /**
     * Get a callable that retrieves the place name for an event post
     *
     * @return callable
     */
    public function getCallable(): callable
    {
        return fn(PostObjectInterface $post): ?string => $this->getPlaceName($post->getSchema());
    }
    /**
     * Retrieves the name of the first place associated with the given event.
     * Falls back to the address if the name is not available.
     *
     * @param Event $event The event object to extract the place name from.
     * @return string|null The name or address of the first place, or null if not available.
     */
    private function getPlaceName(Event $event): ?string
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
