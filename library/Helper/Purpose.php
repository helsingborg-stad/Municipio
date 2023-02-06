<?php

namespace Municipio\Helper;

use Municipio\Helper\Controller as ControllerHelper;

class Purpose
{
    public static function getRegisteredPurposes(): array
    {
        $purposes = [];

        foreach (ControllerHelper::getControllerPaths() as $path) {
            // echo '<pre>' . print_r($path, true) . '</pre>';
        }
        return $purposes;
    }

    public static function getPurposes()
    {
    }
}
