<?php

namespace Municipio\Theme;

class Support
{
    public function __construct()
    {
        self::removeActions();
        self::addActions();
        self::addFilters();
        self::removeTheGenerator();
        self::removeXmlRpc();
        self::removeGravatar();

        add_action('template_redirect', array($this, 'blockAuthorPages'), 5);
        add_action('init', array($this, 'removePostPostType'), 11);

        add_filter('upload_mimes', array($this, 'mimes'));

        // Remove rest api links from head
        remove_action('wp_head', 'rest_output_link_wp_head', 10);
        remove_action('wp_head', 'wp_oembed_add_discovery_links', 10);

        //Remove dashboard stuff
        add_action('wp_dashboard_setup', array($this, 'removeDashboardMetaboxes'));

        //Attachment pages
        add_action('template_redirect', array($this, 'attachmentPageRedirect'));
    }

    public function attachmentPageRedirect()
    {
        if (!defined('ATTACHMENT_PAGE') || defined('ATTACHMENT_PAGE') && ATTACHMENT_PAGE === false) {
            if (is_attachment() && !is_search() && !is_archive()) {
                wp_redirect(wp_get_attachment_url($post->ID));
            }
        }
    }

    /**
     * Append to list of supported mime types
     * @param  array $mimes Original mimes
     * @return array
     */
    public function mimes($mimes)
    {
        $mimes['svg'] = 'image/svg+xml';
        return $mimes;
    }

    /**
     * Removes the post type "post/page" from admin
     * @return NULL
     */
    public function removePostPostType()
    {
        global $wp_post_types;

        if (isset($wp_post_types['post'])) {
            if (function_exists('get_field') && get_field('disable_default_blog_post_type', 'option')) {
                add_action('admin_menu', function () {
                    remove_menu_page('edit.php');
                });

                add_action('wp_before_admin_bar_render', function () {
                    global $wp_admin_bar;
                    $wp_admin_bar->remove_menu('new-post');
                });
            }
        }

        if (isset($wp_post_types['page'])) {
            if (function_exists('get_field') && get_field('disable_default_page_post_type', 'option')) {
                add_action('admin_menu', function () {
                    remove_menu_page('edit.php?post_type=page');
                });

                add_action('wp_before_admin_bar_render', function () {
                    global $wp_admin_bar;
                    $wp_admin_bar->remove_menu('new-page');
                });
            }
        }

        return null;
    }

    /**
     * Removes unwanted actions.
     */
    private static function removeActions()
    {
        remove_action('wp_head', 'print_emoji_detection_script', 7);
        remove_action('wp_print_styles', 'print_emoji_styles');
        remove_action('wp_head', 'rsd_link');
        remove_action('wp_head', 'wlwmanifest_link');
    }

    /**
     * Add actions.
     */
    private static function addActions()
    {
        add_action('after_setup_theme', '\Municipio\Theme\Support::themeSupport');
    }

    /**
     * Add filters.
     */
    private static function addFilters()
    {
        add_filter('intermediate_image_sizes_advanced', '\Municipio\Theme\Support::filterThumbnailSizes');
        //add_filter('gettext', '\Municipio\Theme\Support::changeDefaultTemplateName', 10, 3);
    }

    /**
     * Add theme support.
     */
    public static function themeSupport()
    {
        add_theme_support('menus');
        add_theme_support('post-thumbnails');
        add_theme_support('html5');
        add_theme_support(
            'post-formats',
            array(
                'aside',
                'gallery',
                'link',
                'image',
                'quote',
                'status',
                'video',
                'audio',
                'chat'
            )
        );
    }

    /**
     * Remove medium thumbnail size for all uploaded images.
     * @param array $sizes Default sizes
     * @return array New sizes
     */
    public static function filterThumbnailSizes($sizes)
    {
        unset($sizes['medium']);

        return $sizes;
    }

    /**
     * Change "Default template" to "Article".
     */
    public static function changeDefaultTemplateName($translation, $text, $domain)
    {
        if ($text == 'Default Template') {
            return __('Article', 'municipio');
        }

        return $translation;
    }

    /**
     * Removes the generator meta tag from <head> & admin-footer.
     */
    public static function removeTheGenerator()
    {
        add_filter('the_generator', create_function('', 'return "";'));
        remove_filter('update_footer', 'core_update_footer');
    }

    /**
     * Removes old xmlrpc (always use API)
     */
    public static function removeXmlRpc()
    {
        add_filter('xmlrpc_enabled', '__return_false');
    }

    /**
     * Removes the gravatar from the adminpanel
     */
    public static function removeGravatar()
    {
        if(is_admin())
        {
            add_filter( 'option_show_avatars', '__return_false' );
        }
    }

    public static function removeDashboardMetaboxes()
    {
        global $wp_meta_boxes;

        // Remove wordpress dashboards
        unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_activity']);
        unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_comments']);
        unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_incoming_links']);
        unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']);
        unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_secondary']);
        unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press']);

        // Remove yoast seo
        unset($wp_meta_boxes['dashboard']['normal']['core']['yoast_db_widget']);
    }

    /**
     * Blocks request to the author pages (?author=<ID>).
     * @return void
     */
    public function blockAuthorPages()
    {
        global $wp_query;

        if (
            (defined('MUNICIPIO_BLOCK_AUTHOR_PAGES') && !MUNICIPIO_BLOCK_AUTHOR_PAGES)
            ||
            (get_field('page_link_to_author_archive', 'option') === true && (!defined('MUNICIPIO_BLOCK_AUTHOR_PAGES') || MUNICIPIO_BLOCK_AUTHOR_PAGES))
        ) {
            return;
        }

        if (is_author() || is_attachment()) {
            $wp_query->set_404();
        }

        if (is_feed()) {
            $author = get_query_var('author_name');
            $attachment = get_query_var('attachment');
            $attachment = (empty($attachment)) ? get_query_var('attachment_id') : $attachment;

            if (!empty($author) || !empty($attachment)) {
                $wp_query->set_404();
                $wp_query->is_feed = false;
            }
        }
    }
}
