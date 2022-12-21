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
        preg_match_all('/^(single|archive)-/i', $controller, $matches);

        $controllers = array(
            str_replace('.blade.php', '', self::camelCase(basename($controller, '.php')))
        );
        

        if (isset($matches[1][0])) {
            $controllers[] = self::camelCase($matches[1][0]);
        }

        foreach (self::getControllerPaths() as $path) {
            foreach ($controllers as $controller) {
                $file = $path . '/' . $controller . '.php';
                
                if (!file_exists($file)) {
                    continue;
                }
                
                return $file;
            }
        }

        return false;
    }

    /**
     * Creates view paths dynamicly
     * @param  array    $viewPaths   All view paths that are statically entered.
     * @return array    $viewPaths  Contains all view paths avabile.
     */
    public static function getControllerPaths($controllerPaths = array())
    {
        $versions = apply_filters('Municipio/blade/controllerVersions', array_reverse(array("", "v3")));

        foreach ($versions as $versionKey => $version) {
            $controllerPaths[] = rtrim(get_stylesheet_directory()  . DIRECTORY_SEPARATOR  . "library" . DIRECTORY_SEPARATOR . "Controller" . DIRECTORY_SEPARATOR . $version, DIRECTORY_SEPARATOR);
           
            $controllerPaths[] = rtrim(get_template_directory()    . DIRECTORY_SEPARATOR  . "library" . DIRECTORY_SEPARATOR . "Controller" . DIRECTORY_SEPARATOR . $version, DIRECTORY_SEPARATOR);
        }
        
        $baseDir = MUNICIPIO_PATH . 'templates/';
        foreach (@glob($baseDir . "*", GLOB_ONLYDIR) as $dir) {
            $controllerPaths[] = $dir;
        }
        
        return apply_filters('Municipio/controllerPaths', array_unique($controllerPaths));
    }

    /**
     * String (words or slug) to camel case
     * e.g taxonomy-department -> TaxonomyDepartment
     * e.g taxonomy_department -> TaxonomyDepartment
     * e.g taxonomy department -> TaxonomyDepartment
     * @param  string $string Hyphen string
     * @return string         Camel cased string
     */
    public static function camelCase($string)
    {
        $cc = preg_replace_callback('/(?:^|-|_|\s)(.?)/', array('self', 'camelCaseParts'), $string);

        if (!empty($cc)) {
            return $cc;
        }

        return $string;
    }

    public static function camelCaseParts($parts)
    {
        return strtoupper($parts[1]);
    }

    /**
     * Get a class's namespace
     * @param  string $classPath Path to the class php file
     * @return string            Namespace or null
     */
    public static function getNamespace($classPath)
    {
        $src = file_get_contents($classPath);

        if (preg_match('/namespace\s+(.+?);/', $src, $m)) {
            return $m[1];
        }

        return null;
    }
}
