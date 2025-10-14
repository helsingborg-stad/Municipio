<?php

namespace Municipio\Controller\ArchiveEvent;

use DateTimeInterface;
use Municipio\Helper\DateFormat;
use Municipio\PostObject\PostObjectInterface;
use Municipio\Schema\Schedule;

class GetEventDate
{
    /**
     * Get the date of the first upcoming event occurrence
     */
    public static function getEventDate(PostObjectInterface $post): ?string
    {
        $schedules             = EnsureArrayOf::ensureArrayOf($post->getSchemaProperty('eventSchedule'), Schedule::class);
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

    private static function getFirstUpcomingEventDateTimeFromArrayOfSchedules(Schedule ...$schedules): ?DateTimeInterface
    {
        $firstUpcomingSchedule = self::getFirstUpcomingScheduleFromArrayOfSchedules(...$schedules);

        if ($firstUpcomingSchedule === null) {
            return null;
        }

        return $firstUpcomingSchedule->getProperty('startDate') ?: null;
    }

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

    private static function getLastUpcomingEventDateTimeFromArrayOfSchedules(Schedule ...$schedules): ?DateTimeInterface
    {
        $lastUpcomingSchedule = self::getLastUpcomingScheduleFromArrayOfSchedules(...$schedules);

        if ($lastUpcomingSchedule === null) {
            return null;
        }

        return $lastUpcomingSchedule->getProperty('endDate') ?: null;
    }

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
