<?php

namespace Municipio\Controller\SingularEvent\Mappers;

use Municipio\Schema\Event;
use Municipio\Schema\Organization;
use WpService\Contracts\Wpautop;

class MapOrganizers implements EventDataMapperInterface
{
    public function map(Event $event): array
    {
        return array_filter(
            $this->ensureArray($event->getProperty('organizer')),
            fn ($organizer) => is_a($organizer, Organization::class)
        );
    }

    private function ensureArray($data): array
    {
        return is_array($data) ? $data : [$data];
    }
}
