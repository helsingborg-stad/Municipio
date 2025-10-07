<?php

namespace Municipio\Controller\SingularEvent\Mappers;

use DateTime;
use Municipio\Schema\Event;

class MapEventIsInthePast implements EventDataMapperInterface
{
    public function __construct(private ?DateTime $eventStartDate = null)
    {
    }

    public function map(Event $event): bool
    {
        if (!is_a($this->eventStartDate, DateTime::class)) {
            return false;
        }

        return $this->eventStartDate->getTimestamp() < time();
    }
}
