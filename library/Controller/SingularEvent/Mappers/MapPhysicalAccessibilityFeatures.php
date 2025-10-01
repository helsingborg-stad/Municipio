<?php

namespace Municipio\Controller\SingularEvent\Mappers;

use Municipio\Schema\Event;

class MapPhysicalAccessibilityFeatures implements EventDataMapperInterface
{
    public function map(Event $event): array
    {
        return $event->getProperty('physicalAccessibilityFeatures') ?? [];
    }
}
