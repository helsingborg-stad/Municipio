<?php

namespace Municipio\Helper;

class File
{
    /**
     * It takes a filename and returns the URL of the file if it exists in the media library
     *
     * @param string filename The name of the file you want to get the URL for.
     *
     * @return ?string The URL of the file.
     */
    public static function getFileUrl(string $filename): ?string
    {
        global $wpdb;
        $meta = $wpdb->get_col(
            $wpdb->prepare(
                "SELECT post_id FROM `$wpdb->postmeta`
                WHERE `meta_key` LIKE '_wp_attached_file'
                AND `meta_value` LIKE '%$filename%'
                LIMIT 1"
            )
        );
        if (!empty($meta)) {
            return \wp_get_attachment_url($meta[0]);
        }
        return null;
    }

    /**
     * Check if a file exists, cache in redis. 
     *
     * @param   string  The file path
     *
     * @return  bool    If the file exists or not.
     */
    public static function fileExists($filePath)
    {
        //Unique cache value
        $uid = "municipio_file_exists_cache_" . md5($filePath); 

        //If in cahce, found
        if(wp_cache_get($uid)) {
            return true;
        }

        //If not in cache, look for it, if found cache. 
        if(file_exists($filePath)) {
            wp_cache_set($uid, true);
            return true;
        }

        //Opsie, file not found
        return false; 
    }
}
