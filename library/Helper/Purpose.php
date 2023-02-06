<?php

namespace Municipio\Helper;

use Municipio\Helper\Controller as ControllerHelper;

class Purpose
{
    public static function getRegisteredPurposes(): array
    {
        $purposes = [];

        foreach (ControllerHelper::getControllerPaths() as $path) {
            if (is_dir($dir = $path . DIRECTORY_SEPARATOR . 'Purpose')) {
                foreach (glob("$dir/*.php") as $filename) {
                    if (str_contains($filename, 'Factory')) {
                        continue;
                    }
                    $class = ControllerHelper::getNamespace($filename) . '\\' . basename($filename, '.php');
                    $purposes[$class::getKey()] = $class::getLabel();
                }
            }
        }
        return $purposes;
    }

    public static function getPurposes()
    {
    }
}
