<?php

namespace Municipio\Helper;

use Municipio\Helper\Navigation;
use Municipio\Helper\Image;

class Post
{
    /**
     * Prepare post object before sending to view
     * Appends useful variables for views (Singular).
     *
     * @param   object   $post    WP_Post object
     *
     * @return  object   $post    Transformed WP_Post object
     */
    public static function preparePostObject($post, $data = null)
    {
        $post = self::complementObject(
            $post, 
            [
                'excerpt',
                'post_content_filtered',
                'post_title_filtered',
                'permalink',
                'terms',
                'post_language',
                'reading_time',
                'quicklinks',
                'call_to_action_items',
                'term_icon'
            ], 
            $data
        ); 
        return \Municipio\Helper\FormatObject::camelCase($post);
    }

    //Alias
    public static function preparePostObjectSingular($post, $data = null) {
        self::preparePostObject($post, $data); 
    }

    /**
     * Prepare post object before sending to view
     * Appends useful variables for views (Archive).
     *
     * @param   object   $post    WP_Post object
     *
     * @return  object   $post    Transformed WP_Post object
     */
    public static function preparePostObjectArchive($post, $data = null)
    {
        $post = self::complementObject(
            $post, 
            [
                'excerpt',
                'post_title_filtered',
                'permalink',
                'terms',
                'reading_time',
                'call_to_action_items',
                'term_icon'
            ], 
            $data
        ); 
        return \Municipio\Helper\FormatObject::camelCase($post);
    }

    /**
     * Add post data on post object
     *
     * @param   object   $postObject    The post object
     * @param   object   $appendFields  Data to append on object
     *
     * @return  object   $postObject    The post object, with appended data
     */
    public static function complementObject($postObject, $appendFields = [], $data = null)
    {
        //Check that a post object is entered
        if (!is_a($postObject, 'WP_Post')) {
            return $postObject;
            throw new WP_Error("Complement object must recive a WP_Post class");
        }

        $appendFields = apply_filters(
            'Municipio/Helper/Post/complementPostObject',
            array_merge([], $appendFields) //Ability to add default
        );

        $postObject->quicklinksPlacement = Navigation::getQuicklinksPlacement($postObject->ID);
        $postObject->hasQuicklinksAfterFirstBlock = false;
        $postObject->displayQuicklinksAfterContent = Navigation::displayQuicklinksAfterContent($postObject->ID);
        if (!empty($postObject->quicklinksPlacement) && $postObject->quicklinksPlacement == 'after_first_block' && has_blocks($postObject->post_content) && isset($data['quicklinksMenuItems'])) {
            $postObject->displayQuicklinksAfterContent = false;
            // Add quicklinks after first block
            foreach (parse_blocks($postObject->post_content) as $key => $block) {
                if (0 == $key) {
                    $postObject->post_content =
                        render_block($block) .
                        render_blade_view(
                            'partials.navigation.fixed-after-block',
                            [
                            'quicklinksMenuItems' => $data['quicklinksMenuItems'],
                            'quicklinksPlacement' => $postObject->quicklinksPlacement,
                            'customizer'          => $data['customizer'],
                            'lang'                => $data['lang'],
                            ]
                        );
                            $postObject->hasQuicklinksAfterFirstBlock = true;
                } else {
                    $postObject->post_content .= render_block($block);
                }
            }
        } else {
            $postObject->displayQuicklinksAfterContent = Navigation::displayQuicklinksAfterContent($postObject->ID);
        }
        // Check if password is required for the post
        $passwordRequired = post_password_required($postObject);


        //Generate excerpt
        if (!$passwordRequired && in_array('excerpt', $appendFields)) {
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

                $postObject->excerpt_shorter =
                wp_trim_words(
                    $postObject->post_content,
                    apply_filters('Municipio/Helper/Post/ExcerptLenghtShorter', 10),
                    apply_filters('Municipio/Helper/Post/MoreTag', "...")
                );

                //No content in post
                if (empty($postObject->excerpt)) {
                    $postObject->excerpt = '<span class="undefined-content">' . __("Item is missing content", 'municipio') . "</span>";
                }
            } else {
                $postObject->excerpt_short = wp_trim_words(
                    $postObject->post_content,
                    apply_filters('Municipio/Helper/Post/ExcerptLenghtShort', 20),
                    apply_filters('Municipio/Helper/Post/MoreTag', "...")
                );
            }
        }
        //Get filtered content
        if (!$passwordRequired && in_array('post_content_filtered', $appendFields)) {
            //Parse lead
            $parts = explode("<!--more-->", $postObject->post_content);

            //Replace builtin css classes to our own
            if (is_array($parts) && count($parts) > 1) {
                //Remove the now broken more block
                foreach ($parts as &$part) {
                    $part = str_replace('<!-- wp:more -->', '', $part);
                    $part = str_replace('<!-- /wp:more -->', '', $part);
                }

                $excerpt = self::replaceBuiltinClasses(self::createLeadElement(array_shift($parts)));
                $content = self::replaceBuiltinClasses(self::removeEmptyPTag(implode(PHP_EOL, $parts)));
            } else {
                $excerpt = "";
                $content = self::replaceBuiltinClasses(self::removeEmptyPTag($postObject->post_content));
            }

            if ($postObject->hasQuicklinksAfterFirstBlock) {
                // Temporarily deactivate wpautop from the_content
                remove_filter('the_content', 'wpautop');
            }

            // Apply the_content
            $content = apply_filters('the_content', $content);

            if ($postObject->hasQuicklinksAfterFirstBlock) {
                // Add wpautop back to the_content
                add_filter('the_content', 'wpautop');
            }

            // Build post_content_filtered
            $postObject->post_content_filtered = $excerpt . $content;
        }


        //Get permalink
        if (in_array('permalink', $appendFields)) {
            $postObject->permalink              = get_permalink($postObject);
        }

        //Get reading time
        if (in_array('reading_time', $appendFields)) {
            $postObject->reading_time = \Municipio\Helper\ReadingTime::getReadingTime($postObject->post_content, 0, true);
        }

        //Get filtered post title
        if (in_array('post_title_filtered', $appendFields)) {
            $postObject->post_title_filtered = apply_filters('the_title', $postObject->post_title, $postObject->ID);
        }

        //Get post tumbnail image
        $postObject->thumbnail          = self::getFeaturedImage($postObject->ID, [400, 225]);
        $postObject->thumbnail_tall     = self::getFeaturedImage($postObject->ID, [390, 520]);
        $postObject->thumbnail_square   = self::getFeaturedImage($postObject->ID, [500, 500]);
        $postObject->featuredImage      = self::getFeaturedImage($postObject->ID, [1080, false]);

        //Append post terms
        if (in_array('terms', $appendFields)) {
            $taxonomiesToDisplay        = $data['taxonomiesToDisplay'] ?? null;
            $postObject->terms          = self::getPostTerms($postObject->ID, true, $taxonomiesToDisplay);
            $postObject->termsUnlinked  = self::getPostTerms($postObject->ID, false, $taxonomiesToDisplay);
        }

        if (!empty($postObject->terms) && in_array('term_icon', $appendFields)) {
            $postObject->termIcon = self::getPostTermIcon($postObject->ID, $postObject->post_type);
        }

        if (in_array('post_language', $appendFields)) {
            $siteLang   = strtolower(get_bloginfo('language'));
            $postLang = strtolower(get_field('lang', $postObject->ID));
            if ($postLang && ($postLang !== $siteLang)) {
                $postObject->post_language = $postLang;
            }
        }
        if ($passwordRequired) {
            $postObject->post_content          = get_the_password_form($postObject);
            $postObject->post_content_filtered = get_the_password_form($postObject);
            $postObject->post_excerpt          = get_the_password_form($postObject);
            $postObject->excerpt               = get_the_password_form($postObject);
            $postObject->excerpt_short         = get_the_password_form($postObject);
        }

        if (in_array('call_to_action_items', $appendFields)) {
            $postObject->call_to_action_items = apply_filters('Municipio/Helper/Post/CallToActionItems', [], $postObject);
        }

        /* Get location data */
        $postObject->location = get_field('location', $postObject->ID);
        if (!empty($postObject->location)) {
            $postObject->location['pin'] = \Municipio\Helper\Location::createMapMarker($postObject);
        } 

        if (!empty($postObject->post_type)) {
            $postObject->contentType = \Modularity\Module\Posts\Helper\ContentType::getContentType($postObject->post_type);
        }

        return apply_filters('Municipio/Helper/Post/postObject', $postObject);
    }

    private static function getPostTermIcon($postId, $postType)
    {
        $taxonomies = get_object_taxonomies($postType);

        $termIcon = [];
        $termColor = false;
        foreach ($taxonomies as $taxonomy) {
            $terms = get_the_terms($postId, $taxonomy);
            if (!empty($terms)) {
                foreach ($terms as $term) {
                    if (empty($termIcon)) {
                        $icon = \Municipio\Helper\Term::getTermIcon($term, $taxonomy);
                        $color = \Municipio\Helper\Term::getTermColor($term, $taxonomy);
                        if (!empty($icon) && !empty($icon['src']) && $icon['type'] == 'icon') {
                            $termIcon['icon'] = $icon['src'];
                            $termIcon['size'] = 'md';
                            $termIcon['color'] = 'white';
                            $termIcon['backgroundColor'] = $color;
                        }

                        if (!empty($color)) {
                            $termColor = $color;
                        }
                    }
                }
            }
        }
        if (empty($termIcon) && !empty($termColor)) {
            $termIcon['backgroundColor'] = $color;
        }
        return \apply_filters('Municipio/Helper/Post/getPostTermIcon', $termIcon);
    }

    /**
     * Get a list of terms to display on each inlay
     *
     * @param integer $postId           The post identifier
     * @param boolean $includeLink      If a link should be included or not
     * @return array                    A array of terms to display
     */
    protected static function getPostTerms($postId, $includeLink = true, $taxonomies = false)
    {
        if (empty($taxonomies) && !is_array($taxonomies)) {
            $taxonomies = get_theme_mod(
                'archive_' . get_post_type($postId) . '_taxonomies_to_display',
                false
            );
        } 

        $termsList = [];
        if (is_array($taxonomies) && !empty($taxonomies)) {
            foreach ($taxonomies as $taxonomy) {
                $terms = wp_get_post_terms($postId, $taxonomy);

                if (!empty($terms)) {
                    foreach ($terms as $term) {
                        $item = [];

                        $item['label'] = $term->name ?? '';

                        if ($includeLink) {
                            $item['href'] = get_term_link($term->term_id);
                        }

                        $item['color'] = get_field('colour', $taxonomy . '_' . $term->term_id);

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
    private static function createLeadElement($lead, $search = '<p>', $replace = '<p class="lead">')
    {
        if (str_contains($lead, '<img')) {
            $lead = \Municipio\Content\Images::normalizeImages($lead);
        }
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
    private static function removeEmptyPTag($string)
    {
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
        $featuredImage = Image::getImageAttachmentData($featuredImageID, $size);

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
        $metaKeys = $wpdb->get_col("
            SELECT DISTINCT {$wpdb->postmeta}.meta_key
            FROM {$wpdb->postmeta}
            LEFT JOIN {$wpdb->posts} ON {$wpdb->postmeta}.post_id = {$wpdb->posts}.ID
            WHERE
            {$wpdb->posts}.post_type = '$posttype'
            LIMIT 50
        ");

        // Filter response, faster than making a more advanced query
        $metaKeys = array_filter($metaKeys, function ($value) {
            if (strpos($value, '_') === 0) {
                return false;
            }
            return true;
        });

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

    public static function replaceBuiltinClasses($content)
    {
        return str_replace(
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

                //Gutenberg block image
                'wp-block-image',
                'wp-element-caption',
                '<figcaption>'
            ],
            [
                'c-image',
                'c-image__caption',
                'c-image__image wp-image-',
                'u-float--left@sm u-float--left@md u-float--left@lg u-float--left@xl u-float--left@xl u-margin__y--2 u-margin__right--2@sm u-margin__right--2@md u-margin__right--2@lg u-margin__right--2@xl u-width--100@xs',
                'u-float--right@sm u-float--right@md u-float--right@lg u-float--right@xl u-float--right@xl u-margin__y--2 u-margin__left--2@sm u-margin__left--2@md u-margin__left--2@lg u-margin__left--2@xl u-width--100@xs',
                '',
                'u-margin__x--auto u-text-align--center',

                //Old inline transition button
                'c-button c-button__filled c-button__filled--primary c-button--md',
                'c-button c-button__filled c-button__filled--secondary c-button--md',
                'c-button c-button__filled c-button__filled--secondary c-button--md',
                'c-button c-button__filled c-button__filled--secondary c-button--md',
                'c-button c-button__filled c-button__filled--secondary c-button--md',

                //Gutenberg block image
                'c-image',
                'c-image__caption',
                '<figcaption class="c-image__caption">'
            ],
            $content
        );
    }
}
