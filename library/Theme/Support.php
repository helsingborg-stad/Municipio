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

        add_filter('srm_max_redirects', array($this, 'srmMaxRedirects'));
        add_action('template_redirect', array($this, 'blockAuthorPages'), 5);
        add_action('init', array($this, 'removePostPostType'), 11);

        // Remove rest api links from head
        remove_action('wp_head', 'rest_output_link_wp_head', 10);
        remove_action('wp_head', 'wp_oembed_add_discovery_links', 10);

        // Add categories to "page" post type
        add_action('init', array($this, 'addPageTaxonomy'));
    }

    /**
     * Removes the post type "post"
     * @return boolean
     */
    public function removePostPostType()
    {
        global $wp_post_types;

        if (isset($wp_post_types['post'])) {
            if (!defined("WP_ENABLE_POSTS") || (defined("WP_ENABLE_POSTS") && WP_ENABLE_POSTS !== true)) {
                add_action('admin_menu', function () {
                    remove_menu_page('edit.php');
                });
            }

            return true;
        }

        return false;
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
        add_filter('gettext', '\Municipio\Theme\Support::changeDefaultTemplateName', 10, 3);
    }

    /**
     * Add theme support.
     */
    public static function themeSupport()
    {
        add_theme_support('menus');
        add_theme_support('post-thumbnails');
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
            return _('Artikel');
        }

        return $translation;
    }

    /**
     * Removes the generator meta tag from <head>.
     */
    public static function removeTheGenerator()
    {
        add_filter('the_generator', create_function('', 'return "";'));
    }

    /**
     * Blocks request to the author pages (?author=<ID>).
     * @return void
     */
    public function blockAuthorPages()
    {
        global $wp_query;

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

    /**
     * Update the default maximum number of redirects to 400.
     */
    public function srmMaxRedirects()
    {
        return 400;
    }

    public function addPageTaxonomy($args)
    {
        $labels = array(
            'name'              => _x('Department', 'taxonomy general name'),
            'singular_name'     => _x('Departments', 'taxonomy singular name'),
            'search_items'      => __('Search Departments'),
            'all_items'         => __('All Departments'),
            'parent_item'       => __('Parent Department'),
            'parent_item_colon' => __('Parent Department:'),
            'edit_item'         => __('Edit Department'),
            'update_item'       => __('Update Department'),
            'add_new_item'      => __('Add New Department'),
            'new_item_name'     => __('New Department Name'),
            'menu_name'         => __('Departments'),
        );

        $args = array(
            'hierarchical'      => true,
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array('slug' => 'department'),
        );

        register_taxonomy('department', array('page'), $args);
    }
}
