<?php

namespace Municipio\PostsList\ViewCallableProviders\Schema\Event;

use DateTimeInterface;
use Municipio\Helper\DateFormat;
use Municipio\Helper\EnsureArrayOf\EnsureArrayOf;
use Municipio\PostObject\PostObjectInterface;
use Municipio\PostsList\ViewCallableProviders\ViewCallableProviderInterface;
use Municipio\Schema\Event;
use Municipio\Schema\Schedule;
use WpService\Contracts\DateI18n;

/**
 * Class GetDate
 *
 * Provides utility methods to retrieve event date information for event archives.
 */
class GetDate implements ViewCallableProviderInterface
{
    public function __construct(
        private DateI18n $wpService,
    ) {}

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
        $schedule = self::getFirstUpcomingScheduleFromArrayOfSchedules(...$schedules) ?? self::getClosestPassedScheduleFromArrayOfSchedules(...$schedules);

        if ($schedule === null || !$schedule->getProperty('startDate') instanceof DateTimeInterface) {
            return null;
        }

        return $this->wpService->dateI18n(
            DateFormat::getDateFormat('date-time'),
            $schedule->getProperty('startDate')->getTimestamp(),
        );
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

    /**
     * Finds the closest passed Schedule from an array of schedules.
     *
     * @param Schedule ...$schedules One or more Schedule objects.
     * @return Schedule|null The closest passed Schedule or null if none found.
     */
    private static function getClosestPassedScheduleFromArrayOfSchedules(Schedule ...$schedules): null|Schedule
    {
        usort(
            $schedules,
            static fn(Schedule $a, Schedule $b) => $b->getProperty('startDate') <=> $a->getProperty('startDate'),
        );
        $now = new \DateTime();
        foreach ($schedules as $schedule) {
            if ($schedule->getProperty('startDate') > $now) {
                continue;
            }

            return $schedule;
        }

        return null;
    }
}
