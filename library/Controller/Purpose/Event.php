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
        $this->key   = 'event';
        $this->label = __('Event', 'municipio');

        $this->secondaryPurpose = [
            'place' => Place::class
        ];
    }
    public function init()
    {
        // Initate secondary purposes
        parent::initSecondaryPurpose();
    }
}
