<?php

namespace Municipio\PostsList\ViewCallableProviders\Schema\Event;

use DateTime;
use Municipio\Helper\EnsureArrayOf\EnsureArrayOf;
use Municipio\PostObject\PostObjectInterface;
use Municipio\PostsList\ViewCallableProviders\ViewCallableProviderInterface;
use Municipio\Schema\Event;
use Municipio\Schema\Schedule;

/**
 * Class EventHasMoreOccasions
 *
 * Provides methods to determine if an event has more than one upcoming occasion.
 */
class EventHasMoreOccasions implements ViewCallableProviderInterface
{
    /**
     * Get a callable that checks if an event has more than one upcoming occasion
     *
     * @return callable
     */
    public function getCallable(): callable
    {
        return fn(PostObjectInterface $post): bool => $this->eventHasMoreOccasions($post->getSchema());
    }

    private function eventHasMoreOccasions(Event $event): bool
    {
        $schedules = EnsureArrayOf::ensureArrayOf($event->getProperty('eventSchedule'), Schedule::class);
        $upcomingSchedules = array_filter(
            $schedules,
            fn(Schedule $schedule): bool => $schedule->getProperty('startDate') >= new DateTime(),
        );

        return count($upcomingSchedules) > 1;
    }
}
