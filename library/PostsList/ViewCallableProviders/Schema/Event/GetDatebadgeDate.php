<?php

namespace Municipio\PostsList\ViewCallableProviders\Schema\Event;

use DateTime;
use DateTimeInterface;
use Municipio\Helper\DateFormat;
use Municipio\Helper\EnsureArrayOf\EnsureArrayOf;
use Municipio\PostObject\PostObjectInterface;
use Municipio\PostsList\ViewCallableProviders\ViewCallableProviderInterface;
use Municipio\Schema\Event;
use Municipio\Schema\Schedule;

/**
 * Class GetDatebadgeDate
 *
 * Provides methods to retrieve the date for the date badge of an event.
 */
class GetDateBadgeDate implements ViewCallableProviderInterface
{
    public function __construct(
        private null|string $dateFrom = 'now',
    ) {}

    /**
     * Get a callable that retrieves the date badge date for an event post
     *
     * @return callable
     */
    public function getCallable(): callable
    {
        return fn(PostObjectInterface $post): null|string => $this->getDatebadgeDate($post->getSchema());
    }

    /**
     * Returns the formatted date string for the first upcoming event schedule.
     *
     * @param Event $event The event object.
     * @return string|null The formatted date string (Y-m-d) or null if no upcoming schedule exists.
     */
    private function getDatebadgeDate(Event $event): null|string
    {
        $schedules = EnsureArrayOf::ensureArrayOf($event->getProperty('eventSchedule'), Schedule::class);
        $firstUpcomingDateTime = self::getFirstUpcomingEventDateTimeFromArrayOfSchedules(...$schedules);

        if ($firstUpcomingDateTime === null) {
            return null;
        }

        return $firstUpcomingDateTime->format(DateFormat::getDateFormat('date'));
    }

    /**
     * Retrieves the DateTimeInterface of the first upcoming event schedule.
     *
     * @param Schedule ...$schedules One or more Schedule objects.
     * @return DateTimeInterface|null The DateTimeInterface of the first upcoming schedule or null if none found.
     */
    private function getFirstUpcomingEventDateTimeFromArrayOfSchedules(Schedule ...$schedules): null|DateTimeInterface
    {
        $schedule = $this->getFirstUpcomingScheduleFromArrayOfSchedules(...$schedules) ?? $this->getClosestPassedScheduleFromArrayOfSchedules(...$schedules);

        if ($schedule === null) {
            return null;
        }

        return $schedule->getProperty('startDate') ?: null;
    }

    /**
     * Finds the first upcoming Schedule from an array of schedules.
     *
     * @param Schedule ...$schedules One or more Schedule objects.
     * @return Schedule|null The first upcoming Schedule or null if none found.
     */
    private function getFirstUpcomingScheduleFromArrayOfSchedules(Schedule ...$schedules): null|Schedule
    {
        usort(
            $schedules,
            static fn(Schedule $a, Schedule $b) => $a->getProperty('startDate') <=> $b->getProperty('startDate'),
        );
        $now = new DateTime($this->dateFrom);
        foreach ($schedules as $schedule) {
            if ($schedule->getProperty('startDate') < $now) {
                continue;
            }

            return $schedule;
        }

        return null;
    }

    private function getClosestPassedScheduleFromArrayOfSchedules(Schedule ...$schedules): null|Schedule
    {
        usort(
            $schedules,
            static fn(Schedule $a, Schedule $b) => $b->getProperty('startDate') <=> $a->getProperty('startDate'),
        );
        $now = new DateTime($this->dateFrom);
        foreach ($schedules as $schedule) {
            if ($schedule->getProperty('startDate') >= $now) {
                continue;
            }

            return $schedule;
        }

        return null;
    }
}
