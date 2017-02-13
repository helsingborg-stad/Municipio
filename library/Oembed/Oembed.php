<?php

namespace Municipio\Oembed;

abstract class Oembed
{
    protected $url;
    protected $html;
    private $playerWrapper;
    protected $params = array();

    public function __construct(string $url, string $html = '', bool $playerWrapper = false)
    {
        $this->url = $url;
        $this->html = $html;
        $this->playerWrapper = $playerWrapper;
    }

    public function fallback() : string
    {
        return $this->html;
    }

    abstract public function output() : string;
    abstract public function getThumbnail();
    abstract public function getParams();
}
