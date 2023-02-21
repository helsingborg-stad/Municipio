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
        $this->key = 'event';
        $this->label = __('Event', 'municipio');

        parent::__construct($this->key, $this->label, ['place' => Place::class]);
    }
}
