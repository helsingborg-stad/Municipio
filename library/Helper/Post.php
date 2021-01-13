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
                $postObject->excerpt = wp_trim_words(
                    $postObject->post_content,
                    apply_filters('Municipio/Helper/Post/ExcerptLenght', 55),
                    apply_filters('Municipio/Helper/Post/MoreTag', "...")
                );

                //Create excerpt if not defined by editor
                $postObject->excerpt_short = wp_trim_words(
                    $postObject->post_content,
                    apply_filters('Municipio/Helper/Post/ExcerptLenghtShort', 20),
                    apply_filters('Municipio/Helper/Post/MoreTag', "...")
                );

                //No content in post
                if(empty($postObject->excerpt)) {
                    $postObject->excerpt = __("Item is missing content", 'municipio'); 
                }

            } else {
                $postObject->excerpt_short = wp_trim_words(
                    $postObject->content,
                    apply_filters('Municipio/Helper/Post/ExcerptLenghtShort', 20),
                    apply_filters('Municipio/Helper/Post/MoreTag', "...")
                );
            }
        }

        //Get permalink
        if(in_array('permalink', $appendFields)) {
            $postObject->permalink              = get_permalink($postObject); 
        }

        //Get filtered content
        if(in_array('post_content_filtered', $appendFields)) {

            //Parse lead
            $parts = explode("<!--more-->", $postObject->post_content);

            if(is_array($parts) && count($parts) > 1) {
                $excerpt = '<p class="lead">' . array_shift($parts) . "</p>";
                $content = implode(PHP_EOL, $parts);  
            } else {
                $excerpt = "";
                $content = $postObject->post_content; 
            }

            //Replace builtin css classes to our own
            $postObject->post_content_filtered  = $excerpt . str_replace(
                [
                    'wp-caption',
                    'c-image-text',
                    'wp-image-',
                    'alignleft', 
                    'alignright', 
                    'alignnone',
                    'aligncenter',

                    //Old inline transition button
                    'btn-theme-first',
                    'btn-theme-second',
                    'btn-theme-third',
                    'btn-theme-fourth',
                    'btn-theme-fifth'
                ],
                [
                    'c-image',
                    'c-image__caption u-margin__top--0',
                    'c-image__image wp-image-',
                    'u-float--left@sm u-float--left@md u-float--left@lg u-float--left@xl u-margin__y--2 u-margin__right--2@sm u-margin__right--2@md u-margin__right--2@lg u-margin__right--2@xl u-width--100@xs', 
                    'u-float--right@sm u-float--right@md u-float--right@lg u-float--right@xl u-margin__y--2 u-margin__left--2@sm u-margin__left--2@md u-margin__left--2@lg u-margin__left--2@xl u-width--100@xs', 
                    '',
                    'u-margin__x--auto',

                    //Old inline transition button
                    'c-button c-button__filled c-button__filled--primary c-button--md',
                    'c-button c-button__filled c-button__filled--secondary c-button--md',
                    'c-button c-button__filled c-button__filled--secondary c-button--md',
                    'c-button c-button__filled c-button__filled--secondary c-button--md',
                    'c-button c-button__filled c-button__filled--secondary c-button--md'
                ], 
                apply_filters('the_content', $content)
            ); 

        }

        //Get filtered post title
        if(in_array('post_title_filtered', $appendFields)) {
            $postObject->post_title_filtered    = apply_filters('the_title', $postObject->post_title); 
        }

        //Get post tumbnail image
        $postObject->thumbnail = self::getFeaturedImage($postObject->ID, [400, 225]); 

        return $postObject; 
    }

    /**
     * Get the post featured image
     *
     * @param integer   $postId         
     * @return array    $featuredImage  The post thumbnail image, with alt and title
     */
    public static function getFeaturedImage($postId, $size = 'full')
    {
        $featuredImageID = get_post_thumbnail_id($postId);
        
        $featuredImageSRC = \get_the_post_thumbnail_url(
            $postId,
            apply_filters('Municipio/Helper/Post/FeaturedImageSize', $size)
        );
        $featuredImageAlt   = get_post_meta($featuredImageID, '_wp_attachment_image_alt', true);
        $featuredImageTitle = get_the_title($featuredImageID);

        $featuredImage = [
            'src' => $featuredImageSRC ? $featuredImageSRC : null,
            'alt' => $featuredImageAlt ? $featuredImageAlt : null,
            'title' => $featuredImageTitle ? $featuredImageTitle : null
        ];

        return \apply_filters('Municipio/Helper/Post/FeaturedImage', $featuredImage);
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
