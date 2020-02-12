<?php 

namespace HelsingborgStad;

use BC\Blade\Blade as BladeInstance;

class GlobalBladeEngine {

    /**
     * Gets previous instance, or create a new if empty/invalid 
     * 
     * @return object The blade obect 
     */
    public static function instance() {

        //Get global
        global $globalBladeEngineInstance; 

        //Check if a instance has been created
        if(!is_a($globalBladeEngineInstance, 'BC\Blade\Blade')) {

            //Require view paths
            if(empty(self::getViewPaths())) {
                throw new \Exception("Error: View paths must be defined before running init.");
            }

            //Clear cache on local instance
            self::maybeClearCache(); 

            //Create cache path
            self::createComponentCachePath(); 

            //Create new blade instance
            $globalBladeEngineInstance = new BladeInstance(
                (array) self::getViewPaths(),
                (string) sys_get_temp_dir() . '/global-blade-engine-cache'
            );

            //Check for newly created instance
            if(!is_a($globalBladeEngineInstance, 'BC\Blade\Blade')) {
                //Error if something went wrong
                throw new \Exception("Error: Could not create new instance of blade compiler class.");
            }
        }

        return $globalBladeEngineInstance; 
    }

    /**
     * Appends/prepends the view path object 
     * 
     * @return string The updated object with controller paths
     */
    public static function addViewPath($path, $prepend = true) : array 
    {
        //Get global
        global $globalBladeEngineInstanceViewPaths; 

        //Make array if undefined
        if(!is_array($globalBladeEngineInstanceViewPaths)) {
            $globalBladeEngineInstanceViewPaths = array(); 
        }

        //Sanitize path
        $path = rtrim($path, "/");

        //Push to location array
        if($prepend === true) {
            if (array_unshift($globalBladeEngineInstanceViewPaths, $path)) {
                return $globalBladeEngineInstanceViewPaths;
            }
        } else {
            if (array_push($globalBladeEngineInstanceViewPaths, $path)) {
                return $globalBladeEngineInstanceViewPaths;
            }
        }
        
        //Error if something went wrong
        throw new \Exception("Error appending controller path: " . $path);
    }

    /**
     * Gets the view paths as array 
     * 
     * @return string The updated object with controller paths
     */
    public static function getViewPaths() : array 
    {
        //Get global
        global $globalBladeEngineInstanceViewPaths; 

        //return global
        if(is_array($globalBladeEngineInstanceViewPaths)) {
            return $globalBladeEngineInstanceViewPaths; 
        }

        //Return empty (undefined)
        return array();
    }

    /**
     * Create a cache dir
     *
     * @return string Local path to the cache path
     */
    private static function createComponentCachePath() : string
    {

        $cachePath = (string) sys_get_temp_dir() . '/global-blade-engine-cache'; 

        if (!file_exists($cachePath)) {
            if (!mkdir($cachePath, 0764, true)) {
                throw new \Exception("Could not create cache folder: " . $cachePath);
            }
        }

        return (string) $cachePath;
    }

    /**
     * Clears blade cache if in dev domain
     *
     * @return boolean True if cleared, false otherwise
     */
    private static function maybeClearCache($objectPath = null)
    {

        $cachePath = (string) sys_get_temp_dir() . '/global-blade-engine-cache'; 

        if(strpos($_SERVER['HTTP_HOST'], '.local') !== false){

            $dir = rtrim($cachePath, "/") . DIRECTORY_SEPARATOR; 

            if (is_dir($dir)) { 

                $objects = array_diff(scandir($dir), array('..', '.'));

                if(is_array($objects) && !empty($objects)) {

                    foreach ($objects as $object) {
                        $objectPath = $dir."/".$object;

                        if(is_dir($objectPath)) {
                            self::maybeClearCache($objectPath); 
                        } else {
                            unlink($objectPath);
                        }
                    }
                }

                rmdir($dir); 
            }
        }
        
        return false; 
    }

}