<?php

namespace Municipio\Helper;

use Municipio\Helper\Navigation;
use Municipio\Helper\Image;
use WP_Post;
use Municipio\Integrations\Component\ImageResolver;
use ComponentLibrary\Integrations\Image\Image as ImageComponentContract;
use Municipio\Helper\Term\Term;
use Municipio\PostObject\Decorators\BackwardsCompatiblePostObject;
use Municipio\PostObject\Decorators\IconResolvingPostObject;
use Municipio\PostObject\Decorators\PostObjectFromWpPost;
use Municipio\PostObject\Icon\Resolvers\CachedIconResolver;
use Municipio\PostObject\Icon\Resolvers\NullIconResolver;
use Municipio\PostObject\Icon\Resolvers\PostIconResolver;
use Municipio\PostObject\Icon\Resolvers\TermIconResolver;
use Municipio\PostObject\PostObject;
use Municipio\PostObject\PostObjectInterface;

/**
 * Class Post
 * @package Municipio\Helper
 */
class Post
{
    //Stores cache in runtime
    public static $runtimeCache = [];

    /**
     * Prepare post object before sending to view
     * Appends useful variables for views (Singular).
     *
     * @param object $post WP_Post object
     * @param mixed $data Additional data for post object
     *
     * @return PostObjectInterface $postObject
     */
    public static function preparePostObject(\WP_Post $post, $data = null): PostObjectInterface
    {
        // Create a unique cache key based on the post ID and serialized data
        $cacheGroup = 'preparePostObject';
        $cacheKey   = md5(serialize(get_object_vars($post)) . '_' . serialize($data));

        if (self::isInCache($cacheGroup, $cacheKey)) {
            return self::getFromCache($cacheGroup, $cacheKey);
        }

        // Perform the original operations
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

        return self::convertWpPostToPostObject($post, $cacheGroup, $cacheKey);
    }

     /**
     * Alias for preparePostObject
     *
     * @param object $post WP_Post object
     * @param mixed $data Additional data for post object
     */
    public static function preparePostObjectSingular(\WP_Post $post, $data = null): void
    {
        self::preparePostObject($post, $data);
    }

    /**
     * Prepare post object before sending to view
     * Appends useful variables for views (Archive).
     *
     * @param   object   $post    WP_Post object
     * @param mixed $data Additional data for post object
     *
     * @return PostObjectInterface $postObject
     */
    public static function preparePostObjectArchive(\WP_Post $post, $data = null): PostObjectInterface
    {
        $cacheGroup = 'preparePostObjectArchive';
        $cacheKey   = md5($post->guid . '_' . serialize($data));

        if (self::isInCache($cacheGroup, $cacheKey)) {
            return self::getFromCache($cacheGroup, $cacheKey);
        }

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

        return self::convertWpPostToPostObject($post, $cacheGroup, $cacheKey);
    }

    /**
     * Alias for preparePostObjectArchive
     *
     * @param string $cacheGroup Cache group
     * @param string $cacheKey Cache key
     * @return bool
     */
    private static function isInCache($cacheGroup, $cacheKey): bool
    {
        if (!isset(self::$runtimeCache[$cacheGroup])) {
            self::$runtimeCache[$cacheGroup] = [];
        }

        return isset(self::$runtimeCache[$cacheGroup][$cacheKey]);
    }

    /**
     * Get post object from cache
     * @param string $cacheGroup Cache group
     * @param string $cacheKey Cache key
     * @return PostObjectInterface
     */
    private static function getFromCache($cacheGroup, $cacheKey): PostObjectInterface
    {
        return self::$runtimeCache[$cacheGroup][$cacheKey];
    }

    /**
     * Prepare post object before sending to view
     *
     * @param WP_Post $post WP_Post object
     * @param string $cacheGroup Cache group
     * @param string $cacheKey Cache key
     * @return PostObjectInterface
     */
    private static function convertWpPostToPostObject(WP_Post $post, string $cacheGroup, string $cacheKey): PostObjectInterface
    {
        $camelCasedPost = \Municipio\Helper\FormatObject::camelCase($post);
        $wpService      = \Municipio\Helper\WpService::get();
        $acfService     = \Municipio\Helper\AcfService::get();

        $postObject = new PostObjectFromWpPost(new PostObject($wpService), $post, $wpService);

        $iconResolver = new TermIconResolver($postObject, $wpService, new Term($wpService, AcfService::get()), new NullIconResolver());
        $iconResolver = new PostIconResolver($postObject, $acfService, $iconResolver);
        $iconResolver = new CachedIconResolver($postObject, $iconResolver);
        $postObject   = new IconResolvingPostObject($postObject, $iconResolver);

        $postObject = new BackwardsCompatiblePostObject($postObject, $camelCasedPost);

        self::$runtimeCache[$cacheGroup][$cacheKey] = $postObject;

        return $postObject;
    }

    /**
     * Add post data on post object
     *
     * @param   object   $postObject    The post object
     * @param   array   $appendFields  Data to append on object
     *
     * @return  object   $postObject    The post object, with appended data
     */
    public static function complementObject(\WP_Post $postObject, array $appendFields = [], $data = null): \WP_Post
    {
        //Check that a post object is entered
        $appendFields = apply_filters(
            'Municipio/Helper/Post/complementPostObject',
            array_merge([], $appendFields) //Ability to add default
        );

        $postObject->quicklinksPlacement           = Navigation::getQuicklinksPlacement($postObject->ID);
        $postObject->hasQuicklinksAfterFirstBlock  = false;
        $postObject->displayQuicklinksAfterContent = Navigation::displayQuicklinksAfterContent($postObject->ID);

        if (
            !empty($postObject->quicklinksPlacement) && $postObject->quicklinksPlacement == 'after_first_block'
            && has_blocks($postObject->post_content) && isset($data['quicklinksMenu']['items'])
        ) {
                $postObject->displayQuicklinksAfterContent = false;
                // Add quicklinks after first block
            foreach (parse_blocks($postObject->post_content) as $key => $block) {
                if (0 == $key) {
                    $postObject->post_content                 =
                    render_block($block) .
                    render_blade_view(
                        'partials.navigation.fixed-after-block',
                        [
                        'quicklinksMenu'      => $data['quicklinksMenu']['items'],
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
            [$excerptContent, $hasExcerpt] = self::getPostExcerpt($postObject);

            $excerptContent = nl2br($excerptContent);

            //Create excerpt if not defined by editor
            $postObject->excerpt =
            $hasExcerpt ? $excerptContent :
            wp_trim_words(
                $excerptContent,
                apply_filters('Municipio/Helper/Post/ExcerptLenght', 55),
                apply_filters('Municipio/Helper/Post/MoreTag', "...")
            );

            //Create excerpt if not defined by editor
            $postObject->excerpt_short =
            $hasExcerpt ? $excerptContent :
            wp_trim_words(
                $excerptContent,
                apply_filters('Municipio/Helper/Post/ExcerptLenghtShort', 20),
                apply_filters('Municipio/Helper/Post/MoreTag', "...")
            );

            $postObject->excerpt_shorter =
            $hasExcerpt ? $excerptContent :
            wp_trim_words(
                $excerptContent,
                apply_filters('Municipio/Helper/Post/ExcerptLenghtShorter', 10),
                apply_filters('Municipio/Helper/Post/MoreTag', "...")
            );

            //No content in post
            if (empty($postObject->excerpt)) {
                $postObject->excerpt = '<span class="undefined-content">' .
                __("Item is missing content", 'municipio') . "</span>";
            }
        }

        //Get filtered content
        if (!$passwordRequired && in_array('post_content_filtered', $appendFields)) {
            $postObject->post_content_filtered = self::getFilteredContent($postObject);
        }

        //Get permalink
        if (in_array('permalink', $appendFields)) {
            $postObject->permalink = get_permalink($postObject);
        }

        //Get reading time
        if (in_array('reading_time', $appendFields)) {
            $postObject->reading_time = \Municipio\Helper\ReadingTime::getReadingTime(
                $postObject->post_content,
                0,
                true
            );
        }

        //Get filtered post title
        if (in_array('post_title_filtered', $appendFields)) {
            $postObject->post_title_filtered = apply_filters('the_title', $postObject->post_title, $postObject->ID);
        }

        //Set time formats
        $postObject->post_time_formatted      = wp_date(
            \Municipio\Helper\DateFormat::getDateFormat('time'),
            strtotime($postObject->post_date)
        );
        $postObject->post_date_time_formatted = wp_date(
            \Municipio\Helper\DateFormat::getDateFormat('date-time'),
            strtotime($postObject->post_date)
        );
        $postObject->post_date_formatted      = wp_date(
            \Municipio\Helper\DateFormat::getDateFormat('date'),
            strtotime($postObject->post_date)
        );

        //Get post tumbnail image
        $postObject->images                    = [];
        $postObject->images['thumbnail_16:9']  = self::getFeaturedImage($postObject->ID, [400, 225]);
        $postObject->images['thumbnail_4:3']   = self::getFeaturedImage($postObject->ID, [520, 390]);
        $postObject->images['thumbnail_1:1']   = self::getFeaturedImage($postObject->ID, [500, 500]);
        $postObject->images['thumbnail_3:4']   = self::getFeaturedImage($postObject->ID, [240, 320]);
        $postObject->images['featuredImage']   = self::getFeaturedImage($postObject->ID, [1080, false]);
        $postObject->images['thumbnail_12:16'] = $postObject->images['thumbnail_3:4'];

        //Get image contract
        if ($thumbnailId = get_post_thumbnail_id($postObject->ID)) {
            $postObject->imageContract = ImageComponentContract::factory(
                (int) $thumbnailId,
                [1920, false],
                new ImageResolver()
            );
        }

        //Deprecated
        $postObject->thumbnail        = $postObject->images['thumbnail_16:9'];
        $postObject->thumbnail_tall   = $postObject->images['thumbnail_4:3'];
        $postObject->thumbnail_square = $postObject->images['thumbnail_1:1'];
        $postObject->featuredImage    = $postObject->images['featuredImage'];

        //Append post terms
        if (in_array('terms', $appendFields)) {
            $taxonomiesToDisplay       = $data['taxonomiesToDisplay'] ?? null;
            $postObject->terms         = self::getPostTerms($postObject->ID, true, $taxonomiesToDisplay);
            $postObject->termsUnlinked = self::getPostTerms($postObject->ID, false, $taxonomiesToDisplay);
        }

        if (in_array('term_icon', $appendFields) && !empty($postObject->terms) && !empty($postObject->post_type)) {
            $postObject->termIcon = self::getPostTermIcon($postObject->ID, $postObject->post_type);
        }

        if (in_array('post_language', $appendFields)) {
            $siteLang = strtolower(get_bloginfo('language') ?? '');
            $postLang = strtolower(get_field('lang', $postObject->ID) ?? $siteLang);
            if ($postLang !== $siteLang) {
                $postObject->post_language = $postLang;
            }
        }

        if ($passwordRequired) {
            $postObject->post_content          = get_the_password_form($postObject);
            $postObject->post_content_filtered = get_the_password_form($postObject);
            $postObject->post_excerpt          = get_the_password_form($postObject);
            $postObject->excerpt               = get_the_password_form($postObject);
            $postObject->excerpt_short         = get_the_password_form($postObject);
            $postObject->excerpt_shorter       = get_the_password_form($postObject);
        }

        if (in_array('call_to_action_items', $appendFields)) {
            $postObject->call_to_action_items = apply_filters(
                'Municipio/Helper/Post/CallToActionItems',
                [],
                $postObject
            );
        }

        return apply_filters('Municipio/Helper/Post/postObject', $postObject);
    }

    /**
     * Get filtered content
     * @param  WP_Post $postObject The post object
     * @return string              The filtered content
     */
    public static function getFilteredContent(object $postObject): string
    {
        //Parse lead
        $parts = explode("<!--more-->", $postObject->post_content);

        //Replace builtin css classes to our own
        if (is_array($parts) && count($parts) > 1) {
            //Remove the now broken more block
            foreach ($parts as &$part) {
                $part = str_replace('<!-- wp:more -->', '', $part);
                $part = str_replace('<!-- /wp:more -->', '', $part);
            }

            $excerpt = self::removeEmptyPTag(array_shift($parts));
            $excerpt = self::createLeadElement($excerpt);
            $excerpt = self::replaceBuiltinClasses($excerpt);
            $excerpt = self::handleBlocksInExcerpt($excerpt);

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
        $excerpt = apply_filters('the_excerpt', $excerpt);
        $content = apply_filters('the_content', $content);

        if ($postObject->hasQuicklinksAfterFirstBlock) {
            // Add wpautop back to the_content
            add_filter('the_content', 'wpautop');
        }

        // Build post_content_filtered
        return $excerpt . $content;
    }

    /*
     * Handle blocks in excerpt.
     * If the excerpt contains blocks, the blocks are rendered and returned.
     * Otherwise, the excerpt is returned as is.
     *
     * @param string $excerpt The post excerpt.
     * @return string The excerpt with blocks rendered.
     */
    private static function handleBlocksInExcerpt(string $excerpt): string
    {
        if (!preg_match('/<!--\s?wp:acf\/[a-zA-Z0-9_-]+/', $excerpt)) {
            return $excerpt;
        }

        $excerpt = apply_filters('the_content', $excerpt);

        return $excerpt;
    }

    /*
    * Get the post excerpt .
    *
    * if the post has a manual excerpt, it is returned after stripping shortcodes .
    * if no manual excerpt is set, and the post content contains < !--more-- > ,
    * the content is divided at the < !--more-- > tag, and the first part is returned .
    * if neither manual excerpt nor < !--more-- > tag is present, the entire post content
    * is returned after stripping shortcodes .
    *
    * @param object $postObject The WP_Post object .
    * @return array[string, bool] .
    */
    private static function getPostExcerpt($postObject): array
    {
        if ($postObject->post_excerpt) {
            return [strip_shortcodes($postObject->post_excerpt), true];
        }

        if (!empty($postObject->post_content) && strpos($postObject->post_content, '<!--more-->')) {
                $divided = explode('<!--more-->', $postObject->post_content);
                return !empty($divided[0]) ? [$divided[0], true] : [$postObject->post_content, false];
        }

        return [strip_shortcodes($postObject->post_content), false];
    }

    /**
     * Get the icon and color associated with terms for a post.
     *
     * Iterates through the taxonomies of the post type and retrieves the terms.
     * For each term, it gets the icon and color using the \Municipio\Helper\Term class.
     * The first found icon and color are used to build an associative array representing
     * the term icon, which includes properties like icon source, size, color, and background color.
     * The resulting array is then filtered using 'Municipio/Helper/Post/getPostTermIcon' filter.
     *
     * @param int    $postId   The post identifier.
     * @param string $postType The post type.
     * @return array The term icon associative array.
     */
    private static function getPostTermIcon($postId, $postType)
    {
        $taxonomies = get_object_taxonomies($postType);
        $termIcon   = [];
        $termColor  = false;
        $termHelper = new \Municipio\Helper\Term\Term(\Municipio\Helper\WpService::get(), \Municipio\Helper\AcfService::get());

        foreach ($taxonomies as $taxonomy) {
            $terms = get_the_terms($postId, $taxonomy);
            if (!empty($terms)) {
                foreach ($terms as $term) {
                    if (empty($termIcon)) {
                        $icon  = $termHelper->getTermIcon($term, $taxonomy);
                        $color = $termHelper->getTermColor($term, $taxonomy);
                        if (!empty($icon) && !empty($icon['src']) && $icon['type'] == 'icon') {
                            $termIcon['icon']            = $icon['src'];
                            $termIcon['size']            = 'md';
                            $termIcon['color']           = 'white';
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

                if (!is_wp_error($terms) && !empty($terms)) {
                    foreach ($terms as $term) {
                        $item = [];

                        $item['label']    = $term->name ?? '';
                        $item['slug']     = $term->slug;
                        $item['taxonomy'] = $term->taxonomy;

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
     * @param integer $postId               Post ID
     * @param string|array $size            Since as a string (full) or an array [400, 400]
     *
     * @return array|false $featuredImage  The post thumbnail image, with alt and title
     */
    public static function getFeaturedImage($postId, $size = 'full')
    {
        $thumbnailId   = get_post_thumbnail_id($postId);
        $featuredImage = !empty($thumbnailId) ? Image::getImageAttachmentData($thumbnailId, $size) : false;


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
    public static function getPosttypeMetaKeys(string $postType)
    {
        if (!isset(self::$runtimeCache['getPostTypeMetaKeys'])) {
            self::$runtimeCache['getPostTypeMetaKeys'] = [];
        }

        if (isset(self::$runtimeCache['getPostTypeMetaKeys'][$postType])) {
            return self::$runtimeCache['getPostTypeMetaKeys'][$postType];
        }

        global $wpdb;
        $metaKeys = $wpdb->get_col("
            SELECT DISTINCT {$wpdb->postmeta}.meta_key
            FROM {$wpdb->postmeta}
            LEFT JOIN {$wpdb->posts} ON {$wpdb->postmeta}.post_id = {$wpdb->posts}.ID
            WHERE
            {$wpdb->posts}.post_type = '$postType'
            LIMIT 50
        ");

        // Filter response, faster than making a more advanced query
        $metaKeys = array_filter($metaKeys, function ($value) {
            if (strpos($value, '_') === 0) {
                return false;
            }
            return true;
        });

        return self::$runtimeCache['getPostTypeMetaKeys'][$postType] = $metaKeys;
    }

    /**
     * Lists all meta-keys existing for the given post
     *
     * @param  string $postId   The post id
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

    /**
     * Replace built-in WordPress and theme-specific classes in the provided content.
     *
     * Replaces various built-in WordPress classes and theme-specific classes in the content
     * with corresponding updated classes. This method is useful for standardizing and customizing
     * the appearance of elements in the content.
     *
     * @param string $content The content to replace classes in.
     * @return string The content with replaced classes.
     */
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
                'u-float--left@sm u-float--left@md u-float--left@lg u-float--left@xl u-float--left@xl u-margin__y--2 
                u-margin__right--2@sm u-margin__right--2@md u-margin__right--2@lg u-margin__right--2@xl 
                u-width--100@xs',
                'u-float--right@sm u-float--right@md u-float--right@lg u-float--right@xl u-float--right@xl 
                u-margin__y--2 u-margin__left--2@sm u-margin__left--2@md u-margin__left--2@lg u-margin__left--2@xl 
                u-width--100@xs',
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
