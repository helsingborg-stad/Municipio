<?php

namespace Modularity\Helper;

class ModuleUsageById
{
    public static function getModuleUsageById($id, $limit = false) 
    {
        global $wpdb;

        $modules = self::getPagesFromModuleUsageById($id, $wpdb);
        $shortcodes = self::getPagesFromShortcodeUsageById($id, $wpdb);

        $result = array_merge($modules, $shortcodes);
        
        if (is_numeric($limit)) {
            return self::limitedModuleUsagePages($result, $limit);
        }

        return $result;
    }

    public static function getPagesFromShortcodeUsageById(string $id, \wpdb $wpdb) 
    {
        $shortcodeQuery = "
        SELECT
            {$wpdb->posts}.ID AS post_id,
            {$wpdb->posts}.post_title,
            {$wpdb->posts}.post_type
        FROM {$wpdb->posts}
        WHERE
            {$wpdb->posts}.post_content LIKE '%[modularity id=\"{$id}\"]%'
            AND {$wpdb->posts}.post_type != 'revision'
        ORDER BY {$wpdb->posts}.post_title ASC
        ";

        return $wpdb->get_results($shortcodeQuery, OBJECT);
    }

    public static function getPagesFromModuleUsageById(string $id, \wpdb $wpdb) 
    {
        $idLength = strlen($id);

        $moduleQuery = "
            SELECT
                {$wpdb->postmeta}.post_id,
                {$wpdb->posts}.post_title,
                {$wpdb->posts}.post_type
            FROM {$wpdb->postmeta}
            LEFT JOIN
                {$wpdb->posts} ON ({$wpdb->postmeta}.post_id = {$wpdb->posts}.ID)
            WHERE
                {$wpdb->postmeta}.meta_key = 'modularity-modules'
                AND ({$wpdb->postmeta}.meta_value LIKE '%s:6:\"postid\";s:{$idLength}:\"{$id}\";%')
                AND {$wpdb->posts}.post_type != 'revision'
            ORDER BY {$wpdb->posts}.post_title ASC
        ";

        return $wpdb->get_results($moduleQuery, OBJECT);
    }

    private static function limitedModuleUsagePages($items, $limit)
    {
        $uniqueItems = self::getUniqueItems($items);

        $sliced = self::getSlicedItems($uniqueItems, $limit);

        return (object) [
            'data' => $sliced,
            'more' => self::getMoreCount($uniqueItems, $sliced)
        ];
    }

    public static function getUniqueItems($items)
    {
        $uniqueItems = [];
        foreach ($items as $item) {
            if (!array_key_exists($item->post_id, $uniqueItems)) {
                $uniqueItems[$item->post_id] = $item;
            }
        }
        
        return $uniqueItems;
    }

    private static function getSlicedItems($uniqueItems, $limit)
    {
        if (count($uniqueItems) > $limit) {
            return array_slice($uniqueItems, $limit);
        }

        return $uniqueItems;
    }

    private static function getMoreCount($uniqueItems, $sliced)
    {
        if (count($uniqueItems) > 0 && count($sliced) > 0) {
            return count($uniqueItems) - count($sliced);
        }
        
        return 0;
    }
}