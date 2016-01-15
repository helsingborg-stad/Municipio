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

        $this->loadTemplateClasses();
    }

    private function loadTemplateClasses()
    {
        $directory = MUNICIPIO_PATH . 'library/Template/Custom/';

        foreach (@glob($directory . "*.php") as $file) {
            $class = '\Municipio\Template\Custom\\' . basename($file, '.php');

            if (class_exists($class)) {
                new $class;
            }
        }
    }
}
