<?php

namespace Municipio\Controller\Purpose;

/**
 * Class Event
 * @package Municipio\Controller\Purpose
 */
class Event extends PurposeFactory
{
    public $view;

    public function __construct()
    {
        $this->view = 'purpose-event';

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
