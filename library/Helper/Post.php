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
        $post = \Municipio\Helper\FormatObject::camelCase($post); 
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
    public static function complementObject($postObject, $appendFields = array('excerpt', 'post_content_filtered', 'post_title_filtered', 'permalink'))
    {

        //Check that a post object is entered
        if(!is_a($postObject, 'WP_Post')) {
            return $postObject; 
            throw new WP_Error("Complement object must recive a WP_Post class"); 
        }

        //More? Less? 
        $appendFields = apply_filters('Municipio/Helper/Post/complementPostObject', $appendFields);

        //Generate excerpt
        if(in_array('excerpt', $appendFields)) {
            if(empty($postObject->post_excerpt)) {
                
                //Create excerpt if not defined by editor
                $postObject->post_excerpt = wp_trim_words(
                    $postObject->post_content,
                    apply_filters('Municipio/Helper/Post/ExcerptLenght', 55),
                    apply_filters('Municipio/Helper/Post/MoreTag', "...")
                );

                //No content in post
                if(empty($postObject->post_excerpt)) {
                    $postObject->post_excerpt = __("Item is missing content", 'municipio'); 
                }
            }
        }

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
