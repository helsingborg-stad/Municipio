<?php

namespace Municipio\Controller\SingularEvent\Mappers;

use DateTime;
use Municipio\Controller\SingularEvent;
use Municipio\Controller\SingularEvent\Mappers\Occasion\Occasion;
use Municipio\Controller\SingularEvent\Mappers\Occasion\OccasionInterface;
use Municipio\Schema\Event;
use Municipio\Schema\Schedule;

class MapOccasions implements EventDataMapperInterface
{
    public function __construct(private string $currentPostPermalink, private ?DateTime $currentlyViewing = null)
    {
    }

    /**
     * @return OccasionInterface[]
     */
    public function map(Event $event): array
    {
        return array_filter(array_map(
            fn(Schedule $schedule) => $this->mapScheduleToOccasion($schedule),
            $this->ensureArrayOfSchedules($event->getProperty('eventSchedule'))
        ));
    }

    private function ensureArrayOfSchedules($data): array
    {
        return array_filter(is_array($data) ? $data : [], fn($item) => is_a($item, Schedule::class));
    }

    private function mapScheduleToOccasion(Schedule $schedule): ?OccasionInterface
    {
        $startDate = $this->getStartDateAsStringFromSchedule($schedule);
        $endDate   = $this->getEndDateAsStringFromSchedule($schedule);

        return !empty($startDate) && !empty($endDate) ? new Occasion(
            $startDate,
            $endDate,
            $this->isCurrentOccasion($schedule),
            $this->getUrlFromSchedule($schedule)
        ) : null;
    }

    private function getStartDateAsStringFromSchedule(Schedule $schedule): ?string
    {
        return $schedule->getProperty('startDate')?->format('Y-m-d H:i');
    }

    private function getEndDateAsStringFromSchedule(Schedule $schedule): ?string
    {
        return $schedule->getProperty('endDate')?->format('Y-m-d H:i');
    }

    private function isCurrentOccasion(Schedule $schedule): bool
    {
        if (!$this->currentlyViewing || !$schedule->getProperty('startDate')) {
            return false;
        }

        return $this->currentlyViewing->getTimestamp() === $schedule->getProperty('startDate')->getTimestamp();
    }

    private function getUrlFromSchedule(Schedule $schedule): string
    {
        $separator = strpos($this->currentPostPermalink, '?') === false ? '?' : '&';
        return $this->currentPostPermalink . $separator . SingularEvent::CURRENT_OCCASION_GET_PARAM . '=' . $schedule->getProperty('startDate')?->format(SingularEvent::CURRENT_OCCASION_DATE_FORMAT);
    }
}
