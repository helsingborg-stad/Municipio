<?php

namespace Municipio\PostsList\ViewUtilities\Schema\Event;

use DateTime;
use DateTimeInterface;
use Municipio\Helper\DateFormat;
use Municipio\Helper\EnsureArrayOf\EnsureArrayOf;
use Municipio\PostObject\PostObjectInterface;
use Municipio\PostsList\ViewUtilities\ViewUtilityInterface;
use Municipio\Schema\Event;
use Municipio\Schema\Schedule;

/**
 * Class GetDatebadgeDate
 *
 * Provides methods to retrieve the date for the date badge of an event.
 */
class GetDatebadgeDate implements ViewUtilityInterface
{
    public function getCallable(): callable
    {
        return fn(PostObjectInterface $post): ?string => $this->getDatebadgeDate($post->getSchema());
    }

    /**
     * Returns the formatted date string for the first upcoming event schedule.
     *
     * @param Event $event The event object.
     * @return string|null The formatted date string (Y-m-d) or null if no upcoming schedule exists.
     */
    private function getDatebadgeDate(Event $event): ?string
    {
        $schedules             = EnsureArrayOf::ensureArrayOf($event->getProperty('eventSchedule'), Schedule::class);
        $firstUpcomingDateTime = self::getFirstUpcomingEventDateTimeFromArrayOfSchedules(...$schedules);

        if ($firstUpcomingDateTime === null) {
            return null;
        }

        return $firstUpcomingDateTime->format(DateFormat::getDateFormat('Y-m-d'));
    }

    /**
     * Retrieves the DateTimeInterface of the first upcoming event schedule.
     *
     * @param Schedule ...$schedules One or more Schedule objects.
     * @return DateTimeInterface|null The DateTimeInterface of the first upcoming schedule or null if none found.
     */
    private static function getFirstUpcomingEventDateTimeFromArrayOfSchedules(Schedule ...$schedules): ?DateTimeInterface
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
    private static function getFirstUpcomingScheduleFromArrayOfSchedules(Schedule ...$schedules): ?Schedule
    {
        usort($schedules, fn(Schedule $a, Schedule $b) => $a->getProperty('startDate') <=> $b->getProperty('startDate'));
        $now = new DateTime();
        foreach ($schedules as $schedule) {
            if ($schedule->getProperty('startDate') >= $now) {
                return $schedule;
            }
        }

        return null;
    }
}
