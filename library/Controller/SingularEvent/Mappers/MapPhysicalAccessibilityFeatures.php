<?php

namespace Municipio\Controller\SingularEvent\Mappers;

use Municipio\Schema\Event;

class MapPhysicalAccessibilityFeatures implements EventDataMapperInterface
{
    public function map(Event $event): array
    {
        return $this->ensureArray($event->getProperty('physicalAccessibilityFeatures'));
    }

    private function ensureArray($data): array
    {
        return is_array($data) ? $data : [$data];
    }
}
