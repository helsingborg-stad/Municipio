<?php

namespace Municipio\Controller\ArchiveEvent;

use DateTimeInterface;
use Municipio\Helper\DateFormat;
use Municipio\Helper\EnsureArrayOf\EnsureArrayOf;
use Municipio\Schema\Event;
use Municipio\Schema\Schedule;

/**
 * Class GetEventDate
 *
 * Provides utility methods to retrieve event date information for event archives.
 */
class GetEventDate
{
    /**
     * Get the date of the first upcoming event occurrence.
     *
     * @param Event $event The event object.
     * @return string|null The formatted date string or null if no upcoming event.
     */
    public static function getEventDate(Event $event): ?string
    {
        $schedules             = EnsureArrayOf::ensureArrayOf($event->getProperty('eventSchedule'), Schedule::class);
        $firstUpcomingDateTime = self::getFirstUpcomingEventDateTimeFromArrayOfSchedules(...$schedules);
        $lastUpcomingDateTime  = self::getLastUpcomingEventDateTimeFromArrayOfSchedules(...$schedules);

        if ($firstUpcomingDateTime === null) {
            return null;
        }

        $dateFormat = DateFormat::getDateFormat('date');

        if ($lastUpcomingDateTime !== null && $firstUpcomingDateTime->format('Y-m-d') !== $lastUpcomingDateTime->format('Y-m-d')) {
            return $firstUpcomingDateTime->format($dateFormat) . ' - ' . $lastUpcomingDateTime->format($dateFormat);
        }

        return $firstUpcomingDateTime->format($dateFormat);
    }

    /**
     * Get the DateTime of the first upcoming event from an array of schedules.
     *
     * @param Schedule ...$schedules List of event schedules.
     * @return DateTimeInterface|null The DateTime of the first upcoming event or null.
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
     * Get the first upcoming schedule from an array of schedules.
     *
     * @param Schedule ...$schedules List of event schedules.
     * @return Schedule|null The first upcoming schedule or null.
     */
    private static function getFirstUpcomingScheduleFromArrayOfSchedules(Schedule ...$schedules): ?Schedule
    {
        usort($schedules, fn(Schedule $a, Schedule $b) => $a->getProperty('startDate') <=> $b->getProperty('startDate'));
        foreach ($schedules as $schedule) {
            if ($schedule->getProperty('startDate') >= date('c')) {
                return $schedule;
            }
        }

        return null;
    }

    /**
     * Get the DateTime of the last upcoming event from an array of schedules.
     *
     * @param Schedule ...$schedules List of event schedules.
     * @return DateTimeInterface|null The DateTime of the last upcoming event or null.
     */
    private static function getLastUpcomingEventDateTimeFromArrayOfSchedules(Schedule ...$schedules): ?DateTimeInterface
    {
        $lastUpcomingSchedule = self::getLastUpcomingScheduleFromArrayOfSchedules(...$schedules);

        if ($lastUpcomingSchedule === null) {
            return null;
        }

        return $lastUpcomingSchedule->getProperty('endDate') ?: null;
    }

    /**
     * Get the last upcoming schedule from an array of schedules.
     *
     * @param Schedule ...$schedules List of event schedules.
     * @return Schedule|null The last upcoming schedule or null.
     */
    private static function getLastUpcomingScheduleFromArrayOfSchedules(Schedule ...$schedules): ?Schedule
    {
        usort($schedules, fn(Schedule $a, Schedule $b) => $b->getProperty('endDate') <=> $a->getProperty('endDate'));
        foreach ($schedules as $schedule) {
            if ($schedule->getProperty('endDate') >= date('c')) {
                return $schedule;
            }
        }

        return null;
    }
}
