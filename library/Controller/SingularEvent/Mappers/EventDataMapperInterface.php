<?php

namespace Municipio\Controller\SingularEvent\Mappers;

use Municipio\Schema\Event;

interface EventDataMapperInterface
{
    /**
     * Map event data to desired format for use in view.
     */
    public function map(Event $event): mixed;
}
