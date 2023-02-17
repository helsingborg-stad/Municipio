<?php

namespace Municipio\Controller\Purpose;

/**
 * Class Event
 * @package Municipio\Controller\Purpose
 */
class Event extends PurposeFactory
{
    public function __construct()
    {
        parent::__construct('event', __('Event', 'municipio'), ['place' => Place::class]);
    }
}
