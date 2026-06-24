<?php

namespace Municipio\Controller\SingularEvent\Mappers;

use DateTime;
use Municipio\Controller\SingularEvent;
use Municipio\Controller\SingularEvent\Mappers\Occasion\Occasion;
use Municipio\Controller\SingularEvent\Mappers\Occasion\OccasionInterface;
use Municipio\Helper\EnsureArrayOf\EnsureArrayOf;
use Municipio\Schema\Event;
use Municipio\Schema\Schedule;

/**
 * Maps event schedules to Occasion objects for singular event views.
 */
class MapOccasions implements EventDataMapperInterface
{
    /**
     * Constructor.
     *
     * @param string $currentPostPermalink The permalink of the current post.
     * @param DateTime|null $currentlyViewing The date/time currently being viewed, or null.
     */
    public function __construct(
        private string $currentPostPermalink,
        private ?DateTime $currentlyViewing = null,
    ) {}

    /**
     * Maps an Event's schedules to an array of OccasionInterface objects.
     *
     * @param Event $event The event to map.
     * @return OccasionInterface[] Array of mapped OccasionInterface objects.
     */
    public function map(Event $event): array
    {
        return array_filter(array_map(
            fn(Schedule $schedule) => $this->mapScheduleToOccasion($schedule),
            EnsureArrayOf::ensureArrayOf($event->getProperty('eventSchedule'), Schedule::class),
        ));
    }

    /**
     * Maps a Schedule to an OccasionInterface object.
     *
     * @param Schedule $schedule The schedule to map.
     * @return OccasionInterface|null The mapped OccasionInterface or null if invalid.
     */
    private function mapScheduleToOccasion(Schedule $schedule): ?OccasionInterface
    {
        $startDate = $this->getStartDateAsStringFromSchedule($schedule);
        $endDate = $this->getEndDateAsStringFromSchedule($schedule);

        return !empty($startDate) && !empty($endDate)
            ? new Occasion(
                $startDate,
                $endDate,
                $this->isCurrentOccasion($schedule),
                $this->getUrlFromSchedule($schedule),
            ) : null;
    }

    /**
     * Gets the start date as a string from a Schedule.
     *
     * @param Schedule $schedule The schedule.
     * @return string|null The formatted start date or null.
     */
    private function getStartDateAsStringFromSchedule(Schedule $schedule): ?string
    {
        return $schedule->getProperty('startDate')?->format('Y-m-d H:i');
    }

    /**
     * Gets the end date as a string from a Schedule.
     *
     * @param Schedule $schedule The schedule.
     * @return string|null The formatted end date or null.
     */
    private function getEndDateAsStringFromSchedule(Schedule $schedule): ?string
    {
        return $schedule->getProperty('endDate')?->format('Y-m-d H:i');
    }

    /**
     * Determines if the given schedule is the current occasion.
     *
     * @param Schedule $schedule The schedule to check.
     * @return bool True if current, false otherwise.
     */
    private function isCurrentOccasion(Schedule $schedule): bool
    {
        if (!$this->currentlyViewing || !$schedule->getProperty('startDate')) {
            return false;
        }

        return $this->currentlyViewing->getTimestamp() === $schedule->getProperty('startDate')->getTimestamp();
    }

    /**
     * Generates a URL for the given schedule.
     *
     * @param Schedule $schedule The schedule.
     * @return string The generated URL.
     */
    private function getUrlFromSchedule(Schedule $schedule): string
    {
        $separator = strpos($this->currentPostPermalink, '?') === false ? '?' : '&';
        return $this->currentPostPermalink . $separator . SingularEvent::CURRENT_OCCASION_GET_PARAM . '=' . $schedule->getProperty('startDate')?->format(SingularEvent::CURRENT_OCCASION_DATE_FORMAT);
    }
}
