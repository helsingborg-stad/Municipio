<?php

namespace Municipio\Controller\SingularEvent\Mappers;

use Municipio\Helper\EnsureArrayOf\EnsureArrayOf;
use Municipio\Schema\Event;
use Municipio\Schema\Organization;
use WpService\Contracts\Wpautop;

class MapOrganizers implements EventDataMapperInterface
{
    public function map(Event $event): array
    {
        return array_filter(
            EnsureArrayOf::ensureArrayOf($event->getProperty('organizer'), Organization::class),
            fn($organizer) => is_a($organizer, Organization::class) && !empty($organizer->getProperty('name')),
        );
    }
}
