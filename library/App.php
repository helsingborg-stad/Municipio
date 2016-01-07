<?php

namespace Municipio;

class App
{
    public function __construct()
    {
        new \Municipio\Theme\Enqueue();
    }
}
