<?php

namespace Municipio;

class App
{
    public function __construct()
    {
        new \Municipio\Template();

        new \Municipio\Theme\Enqueue();
        new \Municipio\Theme\Support();
        new \Municipio\Theme\Sidebars();
        new \Municipio\Theme\Navigation();

        new \Municipio\Module\Card();
    }
}
