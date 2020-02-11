<?php 
namespace BladeComponentLibrary;

use HelsingborgStad\Blade\Blade as Blade;

class Render
{
    private $utility;
    private $utilitySlug;
    private $utilityViewName;
    private $utilityArgs;
    private $utilityControllerName;

    public function __construct($slug, $args) {

        //Get utility object
        $utility = Register::$data;

        //Check if utility exists
        if(!isset($utility->{$slug})) {
            die("Utility '" . $slug . "' is not registered.");
        }

        //Set current utility
        $this->utility = $utility->{$slug};

        //Merge arguments
        $this->utilityArgs = $this->mergeArgs($this->utility->args, $args);

        //Set current utility slug
        $this->utilitySlug = $slug;

        //Set current utility view name
        $this->utilityViewName = $this->cleanViewName($this->utility->view);

        //Get the utility controller name
        $this->utilityControllerName = $this->camelCase(
            $this->cleanViewName($this->utility->view)
        ); 

        //Create & get cache path
        $this->createUtilityCachePath(); 
    }

    /**
     * Render a view
     * 
     * @return string The rendered view 
     */
    public function render()
    {
        //Init blade 
        $blade = new Blade(
            (array) Register::$viewPaths, 
            (string) Register::$cachePath
        );

        //Locate the controller
        $controller = $this->locateController($this->utilityControllerName); 

        //Run controller & fetch data
        if($controller != false) {
            $controller = (string) $this->getNamespace($controller) . "\\" . $this->utilityControllerName;
            $controller = new $controller;
            $controllerData = $controller->getData();
        } else {
            $controllerData = array(); 
        }

        //Render view 
        return $blade->view()->make(
            (string) $this->utilityViewName, 
            (array)  array_merge($this->utilityArgs, $controllerData)
        )->render();
    }

    /**
     * Remove .blade.php from view name
     * 
     * @return string Simple view name without appended filetype
     */
    public function cleanViewName($viewName) : string 
    {
        return (string) str_replace('.blade.php', '', $viewName);
    }

    /**
     * Create a cache dir
     * 
     * @return string Local path to the cache path
     */
    private function createUtilityCachePath() : string 
    {
        if (!file_exists(Register::$cachePath)) {
            if (!mkdir(Register::$cachePath, 0764, true)) {
                throw new Exception("Could not create cache folder: " . Register::$cachePath);
            }
        }

        return (string) Register::$cachePath; 
    }

    /**
     * Merge attributes fallback to default
     * 
     * @return string Arguments array merged with default and local
     */
    private function mergeArgs($defaultArgs, $localArgs) : array 
    {
        return array_merge(
            (array) $defaultArgs, 
            (array) $localArgs
        ); 
    }

    /**
     * Creates a camelcased string from hypen based string
     * 
     * @return string The expected controller name
     */
    public function camelCase($viewName) : string 
    {
        return (string) str_replace(
            " ", "", ucwords(
                str_replace('-', ' ', $viewName)
            )
        );
    }

    /**
     * Tries to locate a controller
     * 
     * @return string Controller path
     */
    public function locateController($controller)
    {

        if(is_array(Register::$controllerPaths) && !empty(Register::$controllerPaths)) {

            foreach (Register::$controllerPaths as $path) {
   
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
     * Get a class's namespace
     * 
     * @param  string $classPath Path to the class php file
     * 
     * @return string            Namespace or null
     */
    public function getNamespace($classPath)
    {
        $src = file_get_contents($classPath);

        if (preg_match('/namespace\s+(.+?);/', $src, $m)) {
            return $m[1];
        }

        return null;
    }
}