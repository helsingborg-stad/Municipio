<?php

namespace Municipio\Controller\Purpose;

/**
 * Class Event
 * @package Municipio\Controller\Purpose
 */
class Event extends PurposeFactory
{
    private $label;
    private $key;

    public function __construct()
    {
        $this->label = __('Event', 'municipio');
        $this->key = 'event';
    }
    public function getLabel(): string
    {
        return $this->label;
    }
    public function getKey(): string
    {
        return $this->key;
    }
}
