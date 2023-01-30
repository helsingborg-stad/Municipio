<?php

namespace Municipio\Helper;

class View
{
    /**
     * Tries to locate a view
     * @param  string $view view name
     * @return string             View path
     */
    public static function locateView(string $view = ''): ?string
    {

        $ext = pathinfo($view, PATHINFO_EXTENSION);

        return '<pre>' . var_export($ext, true) . '</pre>';
        foreach (self::getViewPaths() as $path) {
            $file = $path . '/' . $view . '.php';

            if (!file_exists($file)) {
                continue;
            }

                return $file;
        }

        return false;
    }
}
