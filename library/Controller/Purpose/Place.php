<?php

namespace Municipio\Controller\Purpose;

/**
 * Class Place
 * @package Municipio\Controller\Purpose
 */
class Place extends PurposeFactory
{
    private $label;
    private $key;

    public function __construct()
    {
        $this->label = __('Place', 'municipio');
        $this->key = 'place';
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
