<?php

namespace Modularity\Helper;

class File
{
    /**
     * Get PHP namespace from file
     * @param  string $source Path to file
     * @return string         Namespace
     */
    public static function getModuleNamespace(string $path) : string
    {        
        if(!self::fileExists($path)) {
            return '';
        }

        $source = self::fileGetContents($path, [
            'length' => 500 //read 500 bytes max
        ]);
        
        if($source === false) {
            add_action('admin_notices', function() use($path) {
                $malfunctionalPlugin = array_pop(get_plugins( "/" . explode( '/', plugin_basename( $path ))[0]));
                printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr('notice notice-error'), esc_html("ERROR: Could not find module definition (file) in " . $malfunctionalPlugin['Name']));
            });
        }

        //Get namespace from file
        preg_match('/^namespace [^\r\n]*/m', $source, $matches);
        if($namespace = array_pop($matches)) {
            $namespace = trim($namespace);
            $namespace = ltrim($namespace, "namespace ");
            $namespace = rtrim($namespace, ";");
            return $namespace;
        }
        return '';
    }

    /**
     * Creates directory if needed
     * @param  string $path
     * @return string
     */
    public static function maybeCreateDir(string $path)
    {
        if (self::fileExists($path, 86400, 0)) {
            return $path;
        }

        mkdir($path, 0777, true);
        return $path;
    }

    /**
     * Check if a file exists, cache in redis. 
     *
     * @param   string  The file path
     * @param   integer Time to store positive result
     * @param   integer Time to store negative result
     *
     * @return  bool    If the file exists or not.
     */
    public static function fileExists($filePath, $expireFound = 0, $expireNotFound = 86400): bool
    {
        //Unique cache value
        $uid = "mod_file_exists_cache_" . md5($filePath); 

        //If in cahce, found
        if(wp_cache_get($uid, __FUNCTION__)) {
            return true;
        }

        //If not in cache, look for it, if found cache. 
        if(file_exists($filePath)) {
            wp_cache_set($uid, true, __FUNCTION__, $expireFound);
            return true;
        }

        //Opsie, file not found
        wp_cache_set($uid, false, __FUNCTION__, $expireNotFound); 
        return false; 
    }

     /**
     * Retrieve the contents of a file and optionally cache the results.
     *
     * @param string $filePath The path to the file to read.
     * @param array $args {
     *     Optional arguments for customizing file retrieval and caching.
     *
     *     @var bool $use_include_path Whether to search for the file in the include_path (default is false).
     *     @var resource $context A stream context resource to be used for opening the file (default is null).
     *     @var int $offset The initial position to seek to in the file (default is 0).
     *     @var int|null $length The maximum length of data to retrieve (default is null, which means read the entire file).
     * }
     * @param int $expire The expiration time for caching in seconds (default is 86400 seconds, or 1 day).
     *
     * @return string|false The contents of the file or false on failure.
     */
    public static function fileGetContents($filePath, $args = [], $expire = 86400): string
    {
        //Allow args to be inputted
        $use_include_path   = isset($args['use_include_path']) ? $args['use_include_path'] : false;
        $context            = isset($args['context']) ? $args['context'] : null;
        $offset             = isset($args['offset']) ? $args['offset'] : 0;
        $length             = isset($args['length']) ? $args['length'] : null;

        //Unique cache value
        $uid = "mod_file_get_contents_cache_" . md5($filePath . md5(json_encode($args))); 

        //If in cahce, found
        $cachedContents = $contents = wp_cache_get($uid, __FUNCTION__); 
        if($cachedContents !== false) {
            return $cachedContents;
        }

        //If not in cache, look for it, if found cache. 
        $contents = file_get_contents(
            $filePath,
            $use_include_path,
            $context,
            $offset,
            $length
        ); 

        //Store in cache
        wp_cache_set($uid, $contents, __FUNCTION__, $expire); 

        //Return results
        return $contents;
    }

    /**
     * Glob files with caching to improve performance.
     *
     * This function uses PHP's glob function to search for files that match the given pattern.
     * It also utilizes caching to store and retrieve the results, improving performance for
     * repeated calls to the same pattern.
     *
     * @param string $pattern      The file glob pattern to search for files.
     * @param int    $flags        Flags to pass to the glob function (optional, default is 0).
     * @param int    $expireFound  Cache expiration time for found results in seconds (optional, default is 0, which means no expiration).
     * @param int    $expireNotFound Cache expiration time for not found results in seconds (optional, default is 86400 seconds or 24 hours).
     *
     * @return array|false An array of matched files or false if no matches were found.
     */
    public static function glob($pattern, $flags = 0, $expireFound = 0, $expireNotFound = 86400)
    {
        // Unique cache value
        $uid = "mod_glob_cache_" . md5($pattern);

        // If in cache, return cached result
        $cachedResult = wp_cache_get($uid, __FUNCTION__);
        if ($cachedResult !== false) {
            return $cachedResult;
        }

        // If not in cache, use glob to search for files
        $result = glob($pattern, $flags);

        // Cache the result
        if ($result !== false) {
            wp_cache_set($uid, $result, __FUNCTION__, $expireFound);
        } else {
            wp_cache_set($uid, [], __FUNCTION__, $expireNotFound);
        }

        return $result;
    }

}
