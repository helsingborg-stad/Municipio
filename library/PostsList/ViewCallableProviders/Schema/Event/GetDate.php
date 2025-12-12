<?php

namespace Municipio\PostsList\ViewCallableProviders\Schema\Event;

use DateTimeInterface;
use Municipio\Helper\DateFormat;
use Municipio\Helper\EnsureArrayOf\EnsureArrayOf;
use Municipio\PostObject\PostObjectInterface;
use Municipio\PostsList\ViewCallableProviders\ViewCallableProviderInterface;
use Municipio\Schema\Event;
use Municipio\Schema\Schedule;

/**
 * Class GetDate
 *
 * Provides utility methods to retrieve event date information for event archives.
 */
class GetDate implements ViewCallableProviderInterface
{
    /**
     * Get a callable that retrieves the date for an event post
     *
     * @return callable
     */
    public function getCallable(): callable
    {
        return fn(PostObjectInterface $post): null|string => $this->getDate($post->getSchema());
    }

    /**
     * Get the date of the first upcoming event occurrence.
     *
     * @param Event $event The event object.
     * @return string|null The formatted date string or null if no upcoming event.
     */
    private function getDate(Event $event): null|string
    {
        $schedules = EnsureArrayOf::ensureArrayOf($event->getProperty('eventSchedule'), Schedule::class);
        $firstUpcomingDateTime = self::getFirstUpcomingEventDateTimeFromArrayOfSchedules(...$schedules);

        if ($firstUpcomingDateTime === null) {
            return null;
        }

        return $firstUpcomingDateTime->format(DateFormat::getDateFormat('date-time'));
    }

    /**
     * Retrieves the DateTimeInterface of the first upcoming event schedule.
     *
     * @param Schedule ...$schedules One or more Schedule objects.
     * @return DateTimeInterface|null The DateTimeInterface of the first upcoming schedule or null if none found.
     */
    private static function getFirstUpcomingEventDateTimeFromArrayOfSchedules(Schedule ...$schedules): null|DateTimeInterface
    {
        $firstUpcomingSchedule = self::getFirstUpcomingScheduleFromArrayOfSchedules(...$schedules);

        if ($firstUpcomingSchedule === null) {
            return null;
        }

        return $firstUpcomingSchedule->getProperty('startDate') ?: null;
    }

    /**
     * Finds the first upcoming Schedule from an array of schedules.
     *
     * @param Schedule ...$schedules One or more Schedule objects.
     * @return Schedule|null The first upcoming Schedule or null if none found.
     */
    private static function getFirstUpcomingScheduleFromArrayOfSchedules(Schedule ...$schedules): null|Schedule
    {
        usort(
            $schedules,
            static fn(Schedule $a, Schedule $b) => $a->getProperty('startDate') <=> $b->getProperty('startDate'),
        );
        $now = new \DateTime();
        foreach ($schedules as $schedule) {
            if ($schedule->getProperty('startDate') < $now) {
                continue;
            }

            return $schedule;
        }

        return null;
    }
}
