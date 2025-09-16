<?php

namespace Municipio\Controller\School;

use DateTime;
use Municipio\Schema\ElementarySchool;
use Municipio\Schema\Event;
use Municipio\Schema\Preschool;

class EventsGenerator implements ViewDataGeneratorInterface
{
    public function __construct(private ElementarySchool|Preschool $school)
    {
    }

    public function generate(): mixed
    {
        if (empty($this->school->getProperty('event'))) {
            return [];
        }

        if (!is_array($this->school->getProperty('event'))) {
            return [$this->formatEvent($this->school->getProperty('event'))];
        }

        return array_map([$this, 'formatEvent'], $this->school->getProperty('event'));
    }

    private function formatEvent(Event $event): array
    {
        return [
            'name'             => $event->getProperty('name'),
            'timestamp'        => !empty($event->getProperty('startDate')) ? $this->getTimestampFromDateTime($event->getProperty('startDate')) : null,
            'startTimeEndTime' => !empty($event->getProperty('startDate')) && !empty($event->getProperty('endDate')) ? sprintf('%s - %s', $event->getProperty('startDate')->format('H:i'), $event->getProperty('endDate')->format('H:i')) : null,
            'description'      => $event->getProperty('description'),
        ];
    }

    private function getTimestampFromDateTime(DateTime $dateTime): int
    {
        return $dateTime->getTimestamp();
    }
}
