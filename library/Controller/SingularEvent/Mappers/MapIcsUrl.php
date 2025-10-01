<?php

namespace Municipio\Controller\SingularEvent\Mappers;

use Municipio\Schema\Event;

class MapIcsUrl implements EventDataMapperInterface
{
    public function map(Event $event): ?string
    {
        /** @var DateTime|null $startDate */
        $startDate = $event->getProperty('startDate');
        /** @var DateTime|null $endDate */
        $endDate = $event->getProperty('endDate');
        $name    = $event->getProperty('name');

        if (!$startDate || !$endDate || !$name) {
            return '';
        }

        if (is_string($startDate)) {
            $startDate = date_create($startDate);

            if (!$startDate) {
                return '';
            }
        }

        if (is_string($endDate)) {
            $endDate = date_create($endDate);

            if (!$endDate) {
                return '';
            }
        }

        return implode("\n", [
            'data:text/calendar;charset=utf8,BEGIN:VCALENDAR',
            'VERSION:2.0',
            'BEGIN:VEVENT',
            'DTSTART:' . $startDate->format('Ymd\THis\Z'),
            'DTEND:' . $endDate->format('Ymd\THis\Z'),
            'SUMMARY:' . $name,
            'END:VEVENT',
            'END:VCALENDAR',
        ]);
    }
}
