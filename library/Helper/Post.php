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
    public static function complementObject(
        $postObject,
        $appendFields = array(
            'excerpt',
            'post_content_filtered',
            'post_title_filtered',
            'permalink',
            'terms'
        )
    ) {

        //Check that a post object is entered
        if (!is_a($postObject, 'WP_Post')) {
            return $postObject;
            throw new WP_Error("Complement object must recive a WP_Post class"); 
        }

        //More? Less? 
        $appendFields = apply_filters('Municipio/Helper/Post/complementPostObject', $appendFields);

        //Generate excerpt
        if (in_array('excerpt', $appendFields)) {
            if (empty($postObject->post_excerpt)) {

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
                if (empty($postObject->excerpt)) {
                    $postObject->excerpt = '<span class="undefined-content">' . __("Item is missing content", 'municipio') . "</span>";
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
        if (in_array('permalink', $appendFields)) {
            $postObject->permalink              = get_permalink($postObject);
        }

        //Get filtered content
        if (in_array('post_content_filtered', $appendFields)) {
            //Parse lead
            $parts = explode("<!--more-->", $postObject->post_content);

            if(is_array($parts) && count($parts) > 1) {

                //Remove the now broken more block
                foreach ($parts as &$part) {
                    $part = str_replace('<!-- wp:more -->', '', $part);
                    $part = str_replace('<!-- /wp:more -->', '', $part);
                }

                $excerpt = self::createLeadElement(array_shift($parts));
                $content = self::removeEmptyPTag(implode(PHP_EOL, $parts));

            } else {
                $excerpt = "";
                $content = self::removeEmptyPTag($postObject->post_content);
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
                    'btn-theme-fifth',

                    //Gutenberg columns
                    'wp-block-columns',

                    //Gutenberg block image
                    'wp-block-image',
                    '<figcaption>'
                ],
                [
                    'c-image',
                    'c-image__caption',
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
                    'c-button c-button__filled c-button__filled--secondary c-button--md',

                    //Gutenberg columns
                    'o-grid o-grid--no-margin@md',

                    //Gutenberg block image
                    'c-image',
                    '<figcaption class="c-image__caption">'
                ],
                apply_filters('the_content', $content)
            );
        }

        //Get filtered post title
        if (in_array('post_title_filtered', $appendFields)) {
            $postObject->post_title_filtered = apply_filters('the_title', $postObject->post_title); 
        }

        //Get post tumbnail image
        $postObject->thumbnail      = self::getFeaturedImage($postObject->ID, [400, 225]);
        $postObject->thumbnail_tall  = self::getFeaturedImage($postObject->ID, [390, 520]);

        //Append post terms
        if (in_array('terms', $appendFields)) {
            $postObject->terms            = self::getPostTerms($postObject->ID);
            $postObject->termsUnlinked    = self::getPostTerms($postObject->ID, false);
        }

        return $postObject;
    }

    /**
     * Get a list of terms to display on each inlay
     *
     * @param integer $postId           The post identifier
     * @param boolean $includeLink      If a link should be included or not
     * @return array                    A array of terms to display
     */
    protected static function getPostTerms($postId, $includeLink = false)
    {
        $taxonomies = get_theme_mod(
            'archive_' . get_post_type($postId) . '_taxonomies_to_display',
            false
        );

        $termsList = [];

        if (is_array($taxonomies) && !empty($taxonomies)) {
            foreach ($taxonomies as $taxonomy) {
                $terms = wp_get_post_terms($postId, $taxonomy);

                if (!empty($terms)) {
                    foreach ($terms as $term) {

                        $item = [];

                        $item['label'] = strtolower($term->name);

                        if ($includeLink) {
                            $item['href'] = get_term_link($term->term_id);
                        }

                        $termsList[] = $item;
                    }
                }
            }
        }

        return \apply_filters('Municipio/Helper/Post/getPostTerms', $termsList, $postId);
    }

    /**
     * Add lead class to first excerpt p-tag
     *
     * @param string $lead      The lead string
     * @param string $search    What to search for
     * @param string $replace   What to replace with
     * @return string           The new lead string
     */
    private static function createLeadElement($lead, $search = '<p>', $replace = '<p class="lead">') {

        $pos = strpos($lead, $search);

        if ($pos !== false) {
            $lead = substr_replace($lead, $replace, $pos, strlen($search));
        } elseif ($pos === false && $lead === strip_tags($lead)) {
            $lead = $replace . $lead . '</p>';
        }

        return self::removeEmptyPTag($lead);
    }

    /**
     * Remove empty ptags from string
     *
     * @param string $string    A string that may contain empty ptags
     * @return string           A string that not contain empty ptags
     */
    private static function removeEmptyPTag($string) {
        return preg_replace("/<p[^>]*>(?:\s|&nbsp;)*<\/p>/", '', $string);
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
