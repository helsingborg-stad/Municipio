<?php

namespace Municipio\Controller\SingularEvent\Mappers;

use DateTime;
use Municipio\Schema\Event;

class MapEventIsInthePast implements EventDataMapperInterface
{
    public function map(Event $event): bool
    {
        $startDate = $event->getProperty('startDate');

        if (!is_a($startDate, DateTime::class)) {
            return false;
        }

        return $startDate->getTimestamp() < time() ? true : false;
    }
}
