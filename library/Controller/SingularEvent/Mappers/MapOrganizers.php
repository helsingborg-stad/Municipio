<?php

namespace Municipio\Controller\SingularEvent\Mappers;

use Municipio\Schema\Event;
use WpService\Contracts\Wpautop;

class MapOrganizers implements EventDataMapperInterface
{
    /**
     * Constructor
     */
    public function __construct(private Wpautop $wpService)
    {
    }

    public function map(Event $event): array
    {
        $organizers = $event->getProperty('organizer') ?? [];
        return !is_array($organizers) ? [$organizers] : $organizers;
    }
}
