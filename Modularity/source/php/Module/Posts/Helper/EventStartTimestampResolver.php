<?php

declare(strict_types=1);

namespace Modularity\Module\Posts\Helper;

use Municipio\Helper\EnsureArrayOf\EnsureArrayOf;
use Municipio\PostObject\PostObjectInterface;
use Municipio\Schema\Event;
use Municipio\Schema\Schedule;

/**
 * Resolves the most relevant event start timestamp from event schedules.
 */
class EventStartTimestampResolver
{
    public function __construct(
        private ?string $dateFrom = 'now',
    ) {}

    /**
     * Returns first upcoming schedule timestamp, or closest passed schedule timestamp.
     */
    public function getTimestamp(PostObjectInterface $post): ?int
    {
        $schema = $post->getSchema();
        if (!$schema instanceof Event) {
            return null;
        }

        $schedules = EnsureArrayOf::ensureArrayOf($schema->getProperty('eventSchedule'), Schedule::class);
        $schedule = $this->getFirstUpcomingSchedule(...$schedules) ?? $this->getClosestPassedSchedule(...$schedules);

        if ($schedule === null) {
            return null;
        }

        $startDate = $schedule->getProperty('startDate');
        if (!$startDate instanceof \DateTimeInterface) {
            return null;
        }

        return $startDate->getTimestamp();
    }

    /**
     * Get first upcoming schedule by start date.
     */
    private function getFirstUpcomingSchedule(Schedule ...$schedules): ?Schedule
    {
        usort(
            $schedules,
            static fn(Schedule $a, Schedule $b) => $a->getProperty('startDate') <=> $b->getProperty('startDate'),
        );

        $now = new \DateTime($this->dateFrom ?? 'now');
        foreach ($schedules as $schedule) {
            if ($schedule->getProperty('startDate') < $now) {
                continue;
            }

            return $schedule;
        }

        return null;
    }

    /**
     * Get closest passed schedule by start date.
     */
    private function getClosestPassedSchedule(Schedule ...$schedules): ?Schedule
    {
        usort(
            $schedules,
            static fn(Schedule $a, Schedule $b) => $b->getProperty('startDate') <=> $a->getProperty('startDate'),
        );

        $now = new \DateTime($this->dateFrom ?? 'now');
        foreach ($schedules as $schedule) {
            if ($schedule->getProperty('startDate') > $now) {
                continue;
            }

            return $schedule;
        }

        return null;
    }
}
