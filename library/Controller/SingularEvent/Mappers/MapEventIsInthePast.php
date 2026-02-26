<?php

namespace Municipio\Controller\SingularEvent\Mappers;

use DateTime;
use DateTimeInterface;
use Municipio\Helper\EnsureArrayOf\EnsureArrayOf;
use Municipio\Schema\Event;
use Municipio\Schema\Schedule;

class MapEventIsInthePast implements EventDataMapperInterface
{
    public function __construct(private ?DateTime $currentlyViewing = null)
    {
    }

    public function map(Event $event): bool
    {
        $endDate = $this->getRelevantEndDate($event);

        if ($endDate === null) {
            return false;
        }

        return $endDate->getTimestamp() < time();
    }

    /**
     * Gets the relevant end date for determining if the event is in the past.
     *
     * If viewing a specific occasion, returns that occasion's end date.
     * Otherwise, returns the latest end date among all schedules.
     *
     * @param Event $event
     * @return DateTimeInterface|null
     */
    private function getRelevantEndDate(Event $event): ?DateTimeInterface
    {
        $schedules = EnsureArrayOf::ensureArrayOf(
            $event->getProperty('eventSchedule'),
            Schedule::class
        );

        if (empty($schedules)) {
            return null;
        }

        // If viewing a specific occasion, get its end date
        if ($this->currentlyViewing !== null) {
            $currentSchedule = $this->findScheduleByStartDate($schedules, $this->currentlyViewing);
            if ($currentSchedule !== null) {
                $endDate = $currentSchedule->getProperty('endDate');
                // If the current schedule has an endDate, use it
                if ($endDate !== null) {
                    return $endDate;
                }
                // Otherwise, fall back to latest endDate
            }
        }

        // Otherwise, get the latest end date among all schedules
        return $this->getLatestEndDate($schedules);
    }

    /**
     * Finds a schedule by its start date.
     *
     * @param Schedule[] $schedules
     * @param DateTime $startDate
     * @return Schedule|null
     */
    private function findScheduleByStartDate(array $schedules, DateTime $startDate): ?Schedule
    {
        foreach ($schedules as $schedule) {
            $scheduleStartDate = $schedule->getProperty('startDate');
            if ($scheduleStartDate && $scheduleStartDate->getTimestamp() === $startDate->getTimestamp()) {
                return $schedule;
            }
        }

        return null;
    }

    /**
     * Gets the latest end date among all schedules.
     *
     * @param Schedule[] $schedules
     * @return DateTimeInterface|null
     */
    private function getLatestEndDate(array $schedules): ?DateTimeInterface
    {
        $latestEndDate = null;

        foreach ($schedules as $schedule) {
            $endDate = $schedule->getProperty('endDate');
            if ($endDate === null) {
                continue;
            }

            if ($latestEndDate === null || $endDate->getTimestamp() > $latestEndDate->getTimestamp()) {
                $latestEndDate = $endDate;
            }
        }

        return $latestEndDate;
    }
}
