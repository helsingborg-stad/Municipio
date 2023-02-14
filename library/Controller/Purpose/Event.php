<?php

namespace Municipio\Controller\Purpose;

use Municipio\Controller\Purpose\Place;

/**
 * Class Event
 * @package Municipio\Controller\Purpose
 */
class Event extends PurposeFactory
{
    public function __construct()
    {
        // Always include Place in Event:
        $place = new Place();
        $place->init();
    }
    public function init()
    {
    }
    public static function getLabel(): string
    {
        return __('Event', 'municipio');
    }
    public static function getKey(): string
    {
        return 'event';
    }
}
