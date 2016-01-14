<?php

namespace Municipio;

class App
{
    public function __construct()
    {
        new \Municipio\Theme\Enqueue();
        new \Municipio\Theme\Support();
        new \Municipio\Theme\Sidebars();

        $this->loadTemplateClasses();
    }

    private function loadTemplateClasses()
    {
        $directory = MUNICIPIO_PATH . 'library/Template/';

        foreach (@glob($directory . "*.php") as $file) {
            $class = '\Municipio\Template\\' . basename($file, '.php');

            if (class_exists($class)) {
                new $class;
            }
        }
    }
}
