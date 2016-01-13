<?php

namespace Municipio;

class App
{
    public function __construct()
    {
        new \Municipio\Acf();

        new \Municipio\Theme\Enqueue();
        new \Municipio\Theme\Support();
        new \Municipio\Theme\Sidebars();
    }
}
