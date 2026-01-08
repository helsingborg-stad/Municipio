<?php

namespace Municipio\PostsList\ViewCallableProviders\Schema\Event;

use DateTime;
use DateTimeInterface;
use Municipio\Controller\SingularEvent;
use Municipio\Helper\EnsureArrayOf\EnsureArrayOf;
use Municipio\PostObject\PostObjectInterface;
use Municipio\PostsList\ViewCallableProviders\ViewCallableProviderInterface;
use Municipio\Schema\Schedule;

class GetPermalink implements ViewCallableProviderInterface
{
    public function __construct(
        private null|string $dateFrom = 'now',
    ) {}

    /**
     * Get a callable that retrieves the permalink for an event post
     *
     * @return callable
     */
    public function getCallable(): callable
    {
        return fn(PostObjectInterface $post): null|string => $this->getPermalink($post);
    }

    /**
     * Returns the formatted permalink string for the first upcoming event schedule.
     *
     * @param PostObjectInterface $post The event post object.
     * @return string The formatted permalink string.
     */
    private function getPermalink(PostObjectInterface $post): string
    {
        $event = $post->getSchema();
        $schedules = EnsureArrayOf::ensureArrayOf($event->getProperty('eventSchedule'), Schedule::class);
        $firstUpcomingDateTime = $this->getFirstUpcomingEventDateTimeFromArrayOfSchedules(...$schedules);

        if ($firstUpcomingDateTime === null) {
            return $post->getPermalink();
        }

        return $this->formatPermalinkWithDate($post, $firstUpcomingDateTime);
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

    private function formatPermalinkWithDate(PostObjectInterface $post, DateTimeInterface $dateTime): string
    {
        $separator = strpos($post->getPermalink(), '?') === false ? '?' : '&';
        return $post->getPermalink() . $separator . SingularEvent::CURRENT_OCCASION_GET_PARAM . '=' . $dateTime->format(SingularEvent::CURRENT_OCCASION_DATE_FORMAT);
    }
}
