<?php

namespace Municipio\Oembed;

abstract class Oembed
{
    protected $url;
    protected $params = array();

    public function __construct($url)
    {
        $this->url = $url;
    }

    abstract public function output() : string;

    abstract public function getThumbnail();
    abstract public function getParams();
}
