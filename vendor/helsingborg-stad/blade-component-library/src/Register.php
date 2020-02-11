<?php

namespace BladeComponentLibrary;

use HelsingborgStad\Blade\Blade as Blade;

class Register
{
    public static $data;
    public static $cachePath = ""; 
    public static $viewPaths = [];
    public static $controllerPaths = [];

    public static function add($slug, $defaultArgs, $view = null)
    {
        //Create utility data object
        if(is_null(self::$data)) {
            self::$data = (object) array();
        }

        //Get view name
        $view = self::getViewName($slug, $view); 

        //Check if valid slug name
        if (self::sanitizeSlug($slug) != $slug) {
            throw new \Exception("Invalid slug (" . $slug . ") provided, must be a lowercase string only containing letters and hypens.");
        } 

        //Check if valid view name
        if ((self::sanitizeSlug($view) . ".blade.php") != $view) {
            throw new \Exception("Invalid view name (" . $view . ") provided, must be a lowercase string only containing letters and hypens (with exception for .blade.php filetype suffix).");
        }

        //Adds to full object
        self::$data->{$slug} = (object) array(
            'slug' => (string) $slug,
            'args' => (object) $defaultArgs,
            'view' => (string) $view
        );

        //Add blade directive 
        Blade::directive('message_type', function($type_id) {
            return "<input id=\"message_type\" type=\"hidden\" name=\"type_id\" value=\"{$type_id}\">";
        });
    }

    /**
     * Updates the cache path 
     * 
     * @return string The new cache path
     */
    public static function setCachePath($path) : string
    {
        return self::$cachePath = $path;
    }

    /**
     * Appends the view path object
     * 
     * @return string The updated object with view paths
     */
    public static function addViewPath($path) : array
    {
        //Sanitize path
        $path = rtrim($path, "/");

        //Push to location array
        if (array_push(self::$viewPaths, $path)) {
            return self::$viewPaths;
        }

        //Error if something went wrong
        throw new \Exception("Error appending view path: " . $path);
    }

    /**
     * Appends the controller path object 
     * 
     * @return string The updated object with controller paths
     */
    public static function addControllerPath($path) : array 
    {
        //Sanitize path
        $path = rtrim($path, "/");

        //Push to location array
        if (array_push(self::$controllerPaths, $path)) {
            return self::$controllerPaths;
        }

        //Error if something went wrong
        throw new \Exception("Error appending controller path: " . $path);
    }

    /**
     * Use defined view or, generate from slug
     * 
     * @return string The view name included filetype
     */
    private static function getViewName($slug, $view = null) : string
    {
        if (is_null($view)) {
            $view = $slug . '.blade.php'; 
        }
        return $view;
    }

    /**
     * Santize string
     * 
     * @return string The string to be sanitized
     */
    private static function sanitizeSlug($string) : string 
    {
        return preg_replace(
            "/[^a-z-]/i", 
            "", 
            str_replace(".blade.php", "", $string)
        );
    }
}