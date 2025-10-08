<?php

namespace Municipio\Controller\SingularEvent\Mappers;

use DateTime;
use Municipio\Controller\SingularEvent\Mappers\Occasion\OccasionInterface;
use Municipio\Schema\Event;

/**
 * Maps Event data to an ICS URL string.
 */
class MapIcsUrlFromOccasion implements EventDataMapperInterface
{
    public function __construct(private ?OccasionInterface $occasion = null)
    {
    }

    /**
     * Maps the given Event to an ICS URL string.
     *
     * @param Event $event
     * @return string|null
     */
    public function map(Event $event): ?string
    {
        if (!$this->occasion) {
            return null;
        }

        $startDate = $this->getStartDate();
        $endDate   = $this->getEndDate();
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
     * Gets the start date
     *
     * @return DateTime
     */
    private function getStartDate(): DateTime
    {
        return new DateTime($this->occasion->getStartDate());
    }

    /**
     * Gets the end date.
     *
     * @return DateTime|null
     */
    private function getEndDate(): ?DateTime
    {
        return new DateTime($this->occasion->getEndDate());
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
