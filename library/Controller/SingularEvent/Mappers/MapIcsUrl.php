<?php

namespace Municipio\Controller\SingularEvent\Mappers;

use Municipio\Schema\Event;

/**
 * Maps Event data to an ICS URL string.
 */
class MapIcsUrl implements EventDataMapperInterface
{
    /**
     * Maps the given Event to an ICS URL string.
     *
     * @param Event $event
     * @return string|null
     */
    public function map(Event $event): ?string
    {
        $startDate = $this->getStartDate($event);
        $endDate   = $this->getEndDate($event);
        $name      = $this->getName($event);

        if (empty($startDate) || empty($endDate) || empty($name)) {
            return null;
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

    /**
     * Gets the start date from the Event.
     *
     * @param Event $event
     * @return \DateTime|null
     */
    private function getStartDate(Event $event): ?\DateTime
    {
        return is_a($event->getProperty('startDate'), \DateTime::class) ? $event->getProperty('startDate') : null;
    }

    /**
     * Gets the end date from the Event.
     *
     * @param Event $event
     * @return \DateTime|null
     */
    private function getEndDate(Event $event): ?\DateTime
    {
        return is_a($event->getProperty('endDate'), \DateTime::class) ? $event->getProperty('endDate') : null;
    }

    /**
     * Gets the name from the Event.
     *
     * @param Event $event
     * @return string|null
     */
    private function getName(Event $event): ?string
    {
        return is_string($event->getProperty('name')) ? $event->getProperty('name') : null;
    }
}
