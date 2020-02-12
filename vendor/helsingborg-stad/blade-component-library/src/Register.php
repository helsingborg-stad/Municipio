<?php

namespace BladeComponentLibrary;

use \HelsingborgStad\GlobalBladeEngine as Blade; 

class Register
{
    public static $data;
    public static $cachePath = ""; 
    public static $viewPaths = [];
    public static $controllerPaths = [];

    private static $_reservedNames = ["data"];

    private static $_controllers = [];

    public static function add($slug, $defaultArgs, $view = null)
    {
        //Create utility data object
        if(is_null(self::$data)) {
            self::$data = (object) array();
        }

        //Prohibit reserved names
        if(in_array($slug, self::$_reservedNames)) {
            throw new \Exception("Invalid slug (" . $slug . ") provided, cannot be used as a view name since it is reserved for internal purposes.");
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
            'slug'       => (string) $slug,
            'args'       => (object) $defaultArgs,
            'view'       => (string) $slug . DIRECTORY_SEPARATOR . $view,
            'controller' => (string) $slug
        );

        //Add include alias
        self::registerComponentAlias($slug);

        // Register view composer
        self::registerViewComposer(self::$data->{$slug});
    }

    /**
     * Appends the controller path object 
     * 
     * @return string The updated object with controller paths
     */
    public static function addControllerPath($path, $prepend = true) : array 
    {
        //Sanitize path
        $path = rtrim($path, "/");

        //Push to location array
        if($prepend === true) {
            if (array_unshift(self::$controllerPaths, $path)) {
                return self::$controllerPaths;
            }
        } else {
            if (array_push(self::$controllerPaths, $path)) {
                return self::$controllerPaths;
            }
        }
        
        //Error if something went wrong
        throw new \Exception("Error appending controller path: " . $path);
    }

    /**
     * Registers components directory
     * 
     * @return string The sluts of all registered components
     */
    public static function registerInternalComponents($path) : array 
    {
        //Declare
        $result = array(); 

        //Sanitize path
        $basePath = rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . "*";

        //Glob
        $locations = glob($basePath); 

        //Loop over each path
        if(is_array($locations) && !empty($locations)) {
            foreach($locations as $path) {

                //Check if folder
                if(!is_dir($path)) {
                    continue; 
                }

                //Locate config file
                $configFile = glob($path . DIRECTORY_SEPARATOR . "*.json"); 

                //Get first occurance of config
                if(is_array($configFile) && !empty($configFile)) {
                    $configFile = array_pop($configFile); 
                } else {
                    throw new \Exception("No config file found in " . $path);
                }

                //Read config
                if(!$configJson = file_get_contents($configFile)) {
                    throw new \Exception("Configuration file unreadable at " . $configFile);
                }

                //Check if valid json
                if(!$configJson = json_decode($configJson, true)) {
                    throw new \Exception("Invalid formatting of configuration file in " . $configFile);
                }

                //Register the component
                self::add(
                    $configJson['slug'],
                    $configJson['default'],
                    $configJson['view'] ? $configJson['view'] : $configJson['slug'] . "blade.php"
                );

                //Log
                $result[] = $configJson['slug']; 
            }
        }

        return $result; 
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

    /**
     * Registers all components as include aliases
     *
     * @return bool
     */
    private static function registerComponentAlias($componentSlug)
    {
        Blade::instance()->component(
            ucfirst($componentSlug)  . '.' . $componentSlug,
            $componentSlug
        );
    }

    public static function registerViewComposer($component)
    {
        Blade::instance()->composer(
            ucfirst($component->slug) . '.' . $component->slug,
            function ($view) use ($component) {

                $controllerName = self::camelCase(
                    self::cleanViewName($component->slug)
                );
                
                $viewData = self::accessProtected($view, 'data');

                // Get controller data
                $controllerArgs = (array) self::getControllerArgs(
                    array_merge((array) $component->args, (array) $viewData),
                    $controllerName
                );

                $view->with($controllerArgs);
            }
        );
    }

    /**
     * Proxy for accessing provate props
     *
     * @return string Array of values
     */
    public static function accessProtected($obj, $prop)
    {
        $reflection = new \ReflectionClass($obj);
        $property = $reflection->getProperty($prop);
        $property->setAccessible(true);
        return $property->getValue($obj);
    }

    /**
     * Get data from controller
     *
     * @return string Array of controller data
     */
    public static function getControllerArgs($data, $controllerName): array
    {
        //Run controller & fetch data
        if ($controllerLocation = self::locateController($controllerName)) {

            $controllerId = md5($controllerLocation); 

            if(in_array($controllerId, self::$_controllers)) {
                $controller = self::$_controllers[$controllerId]; 
            } else {
                $controller = (string)("\\".self::getNamespace($controllerLocation)."\\".$controllerName);
                $controller = new $controller($data);
            }

            return $controller->getData();
        }

        return array();
    }

    /**
     * Creates a camelcased string from hypen based string
     *
     * @return string The expected controller name
     */
    public static function camelCase($viewName): string
    {
        return (string)str_replace(
            " ",
            "",
            ucwords(
                str_replace('-', ' ', $viewName)
            )
        );
    }

    /**
     * Locates a controller
     *
     * @return string Controller path
     */
    public static function locateController($controller)
    {

        if (is_array(Register::$controllerPaths) && !empty(Register::$controllerPaths)) {

            foreach (Register::$controllerPaths as $path) {

                $file = $path.DIRECTORY_SEPARATOR.$controller.DIRECTORY_SEPARATOR.$controller.'.php';

                if (!file_exists($file)) {
                    continue;
                }

                return $file;
            }
        }

        return false;
    }

    /**
     * Get a class's namespace
     *
     * @param  string $classPath Path to the class php file
     *
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

    /**
     * Remove .blade.php from view name
     *
     * @return string Simple view name without appended filetype
     */
    public static function cleanViewName($viewName): string
    {
        return (string) str_replace('.blade.php', '', $viewName);
    }
}