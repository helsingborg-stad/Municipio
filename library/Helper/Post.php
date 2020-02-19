<?php

namespace Municipio\Helper;

class Post
{
    /**
     * Prepare post object before sending to view
     * Appends useful variables for views (generic). 
     * 
     * @param   object   $post    WP_Post object
     * 
     * @return  object   $post    Transformed WP_Post object
     */
    public static function preparePostObject($post) {
        $post = self::complementObject($post);
        $post = self::camelCaseObject($post); 
        return $post; 
    }

    /**
     * Add post data on post object
     * 
     * @param   object   $postObject    The post object
     * @param   object   $appendFields  Data to append on object
     * 
     * @return  object   $postObject    The post object, with appended data
     */
    public static function complementObject($postObject, $appendFields = array('post_content_filtered', 'post_title_filtered', 'permalink'))
    {

        //Check that a post object is entered
        if(!is_a($postObject, 'WP_Post')) {
            return $postObject; 
            throw new WP_Error("Complement object must recive a WP_Post class"); 
        }

        //More? Less? 
        $appendFields = apply_filters('Municipio/Post/complementPostObject', $appendFields); 

        //Get permalink
        if(in_array('permalink', $appendFields)) {
            $postObject->permalink              = get_permalink($postObject); 
        }

        //Get filtered content
        if(in_array('post_content_filtered', $appendFields)) {
            $postObject->post_content_filtered  = apply_filters('the_content', $postObject->post_content); 
        }

        //Get filtered post title
        if(in_array('post_title_filtered', $appendFields)) {
            $postObject->post_title_filtered    = apply_filters('the_title', $postObject->post_title); 
        }

        return $postObject; 
    }

    /**
     * Replaces old keys with new (recursivley)
     * 
     * @param   function    $func    Function for transformation of key
     * @param   array       $array   The array to filter
     * 
     * @return  array       $return  The array with renamed keys
     */
    public static function mapArrayKeys(callable $func, array $array) {
        $return = array();
        foreach ($array as $key => $value) {
          $return[$func($key)] = is_array($value) ? self::mapArrayKeys($func, $value) : $value;
        }
        return $return;
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
        return (object) self::mapArrayKeys(function($string) {
            return lcfirst(implode('', array_map('ucfirst', explode('_', strtolower($string)))));
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
