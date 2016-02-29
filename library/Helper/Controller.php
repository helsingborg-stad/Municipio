<?php

namespace Municipio\Helper;

class Controller
{
    /**
     * Tries to locate a controller
     * @param  string $controller Controller name
     * @return string             Controller path
     */
    public static function locateController($controller)
    {
        $searchPaths = array(
            get_stylesheet_directory() . '/library/Controller',
            get_template_directory() . '/library/Controller',
        );

        /**
         * Apply filter to $searchPaths
         * @since 0.1.0
         * @var   array
         */
        $searchPaths = apply_filters('Municipio/blade/controllers_search_paths', $searchPaths);

        foreach ($searchPaths as $path) {
            $file = $path . '/' . str_replace('.blade.php', '', self::camelCase(basename($controller, '.php'))) . '.php';

            if (!file_exists($file)) {
                continue;
            }

            return $file;
        }

        return false;
    }

    /**
     * Hyphen slug to camel case
     * e.g taxonomy-department -> TaxonomyDepartment
     * @param  string $string Hyphen string
     * @return string         Camel cased string
     */
    public static function camelCase($string)
    {
        return @preg_replace('/(?:^|-)(.?)/e', "strtoupper('$1')", $string);
    }

    /**
     * Get a class's namespace
     * @param  string $classPath Path to the class php file
     * @return string            Namespace or null
     */
    public static function getNamespace($classPath)
    {
        $src = file_get_contents($classPath);

        if (preg_match('#^namespace\s+(.+?);$#sm', $src, $m)) {
            return $m[1];
        }

        return null;
    }
}
