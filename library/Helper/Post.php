<?php

namespace Municipio\Helper;

class Post
{
    /**
     * Add filtered data to post object
     * 
     */
    public static function complementObject($postObject, $appendFields = array('post_content_filtered', 'post_title_filtered', 'permalink'))
    {
        if(!is_a($postObject, 'WP_Post')) {
            return $postObject; 
        }

        $appendFields = apply_filters('Municipio/Post/complementPostObject', $appendFields); 

        if(in_array('permalink', $appendFields)) {
            $postObject->permalink              = get_permalink($postObject); 
        }

        if(in_array('post_content_filtered', $appendFields)) {
            $postObject->post_content_filtered  = apply_filters('the_content', $postObject->post_content); 
        }

        if(in_array('post_title_filtered', $appendFields)) {
            $postObject->post_title_filtered    = apply_filters('the_title', $postObject->post_title); 
        }

        return $postObject; 
    }

    public static function mapArrayKeys(callable $f, array $xs) {
        $out = array();
        foreach ($xs as $key => $value) {
          $out[$f($key)] = is_array($value) ? mapArrayKeys($f, $value) : $value;
        }
        return $out;
      }

    /**
     * Camel case snake_case object 
     * 
     * @param   object   $postObject The post object, snake case
     * 
     * @return  object   $postObject The post object, camel case
     */
    public static function camelCaseObject($postObject)
    {
        return (object) self::mapArrayKeys(function($str) {
            return lcfirst(implode('', array_map('ucfirst', explode('_', $str))));
        }, (array) $postObject);
    }


    /**
     * Lists all meta-keys existing for the given posttype
     *
     * Attention: Since this method is using the database to get the
     * metadata keys there need to be posts in the posttype to get results
     *
     * @param  string $posttype The posttype
     * @return array            Meta keys as array
     */
    public static function getPosttypeMetaKeys($posttype)
    {
        global $wpdb;
        $metaKeys = $wpdb->get_results("
            SELECT DISTINCT {$wpdb->postmeta}.meta_key
            FROM {$wpdb->postmeta}
            LEFT JOIN {$wpdb->posts} ON {$wpdb->postmeta}.post_id = {$wpdb->posts}.ID
            WHERE
                {$wpdb->posts}.post_type = '$posttype'
                AND NOT LEFT({$wpdb->postmeta}.meta_key, 1) = '_'
        ");

        return $metaKeys;
    }

    /**
     * Lists all meta-keys existing for the given post
     *
     * @param  string $post     The post id
     * @return array            Meta keys as array
     */
    public static function getPostMetaKeys($postId)
    {
        global $wpdb;
        $metaKeys = $wpdb->get_results("
            SELECT DISTINCT {$wpdb->postmeta}.meta_key
            FROM {$wpdb->postmeta}
            WHERE
                {$wpdb->postmeta}.post_id = {$postId}
                AND NOT LEFT({$wpdb->postmeta}.meta_key, 1) = '_'
        ");

        return $metaKeys;
    }
}
