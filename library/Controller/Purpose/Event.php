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
