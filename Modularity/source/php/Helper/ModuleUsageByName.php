<?php

namespace Modularity\Helper;

use Modularity\Helper\ModuleUsageById;

class ModuleUsageByName
{
    public static function getModuleUsageByName($postType)
    {
        $modules    = self::getPagesByModuleName($postType);
        $blocks     = self::getBlocksByModuleName($postType);
        
        return array_unique(array_merge($modules, $blocks));
    }

    public static function getBlocksByModuleName($postType)
    {
        global $wpdb;

        $postType = str_replace('mod-', 'acf/', $postType);
        $pages = $wpdb->get_results(
            "SELECT *
            FROM $wpdb->posts
            WHERE post_content LIKE '%{$postType}%'
            AND post_status = 'publish'"
        );
        
        return is_array($pages) ? array_column($pages, 'ID') : [];
    }

    public static function getPagesByModuleName($postType)
    {
        $posts = self::getPostsByType($postType);

        $pages = [];
        foreach ($posts as $post) {
            if (empty($post->ID)) {
                continue;
            }

            $moduleUsageOnPages = self::getModuleUsageById($post->ID);
            if (!empty($moduleUsageOnPages)) {
                $pages = array_merge($pages, array_column($moduleUsageOnPages, 'post_id'));
            }
        }


        return is_array($pages) ? $pages : [];
    }

    public static function getPostsByType($postType)
    {
        $args = array(
            'post_type'      => $postType,
            'posts_per_page' => -1,
            'post_status'    => 'publish',
        );

        return get_posts($args);
    }

    private static function getModuleUsageById($postId)
    {
        return ModuleUsageById::getModuleUsageById($postId);
    }
}
