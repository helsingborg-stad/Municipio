<?php

namespace Municipio\Controller\ArchiveEvent;

use DateTimeInterface;
use Municipio\Helper\DateFormat;
use Municipio\Schema\Event;
use Municipio\Schema\Schedule;

class GetDatebadgeDate
{
    public static function getDatebadgeDate(Event $event): ?string
    {
        $schedules             = EnsureArrayOf::ensureArrayOf($event->getProperty('eventSchedule'), Schedule::class);
        $firstUpcomingDateTime = self::getFirstUpcomingEventDateTimeFromArrayOfSchedules(...$schedules);

        if ($firstUpcomingDateTime === null) {
            return null;
        }

        return $firstUpcomingDateTime->format(DateFormat::getDateFormat('Y-m-d'));
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
}
