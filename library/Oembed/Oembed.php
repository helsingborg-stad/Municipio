<?php

namespace Municipio\Oembed;

abstract class Oembed
{
    protected $url;
    protected $html;
    protected $params = array();

    public function __construct(string $url, string $html = '')
    {
        $this->url = $url;
        $this->html = $html;
    }

    public function fallback() : string
    {
        return $this->html;
    }

    abstract public function output() : string;
    abstract public function getThumbnail();
    abstract public function getParams();
}
