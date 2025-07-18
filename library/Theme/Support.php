<?php

namespace Municipio\Theme;

use WP_Post;
class Support
{
    public function __construct()
    {
        self::removeActions();
        self::addActions();
        self::removeTheGenerator();
        self::removeXmlRpc();
        self::removeGravatar();

        add_action('template_redirect', array($this, 'blockAuthorPages'), 5);
        add_action('init', array($this, 'removePostPostType'), 11);

        add_filter('upload_mimes', array($this, 'mimes'));
        add_filter('wp_check_filetype_and_ext', [$this, 'allowMultipleDwgMimeTypes'], 10, 4);
        add_filter('wp_check_filetype_and_ext', [$this, 'allowMultipleDotxMimeTypes'], 10, 4);

        // Remove rest api links from head
        remove_action('wp_head', 'rest_output_link_wp_head', 10);
        remove_action('wp_head', 'wp_oembed_add_discovery_links', 10);

        //Remove dashboard stuff
        add_action('wp_dashboard_setup', array($this, 'removeDashboardMetaboxes'));

        //Attachment pages
        add_action('template_redirect', array($this, 'attachmentPageRedirect'));
    }

    /**
     * Redirects attachment pages to the file URL or sets 404 if attachment page is not allowed.
     * 
     * @return void
     */
    public function attachmentPageRedirect(): void
    {
        $redirectFromAttachmentPageToFile   = (bool) (defined('ATTACHMENT_PAGE_REDIRECT') ? constant('ATTACHMENT_PAGE_REDIRECT') : true);
        $allowAttachmentPage                = (bool) (defined('ALLOW_ATTACHMENT_PAGE') ? constant('ALLOW_ATTACHMENT_PAGE') : false);
        
        if ($redirectFromAttachmentPageToFile && $this->isAttachment() && !is_search() && !is_archive()) {
            global $post;
            if ($post instanceof WP_Post) {
                wp_redirect(wp_get_attachment_url($post->ID));
                exit;
            }
        }

        if(!$allowAttachmentPage && is_attachment()) {
            global $wp_query;
            $wp_query->set_404();
        }
    }

    /**
     * Checks if the current post is an attachment.
     * 
     * @return bool
     */
    public function isAttachment() : bool
    {
        global $post;
        if ($post instanceof WP_Post) {
            return $post->post_type === 'attachment';
        }
        return false;
    }

    /**
     * Append to list of supported mime types
     * @param  array $mimes Original mimes
     * @return array
     */
    public function mimes($mimes)
    {
        $mimes['svg']  = 'image/svg+xml';
        $mimes['dwg']  = 'image/vnd.dwg';
        $mimes['dotx'] = 'application/vnd.openxmlformats-officedocument.wordprocessingml.template';
        $mimes['docm'] = 'application/vnd.ms-word.document.macroEnabled.12';
        $mimes['xlsm'] = 'application/vnd.ms-excel.sheet.macroEnabled.12';
        $mimes['pptm'] = 'application/vnd.ms-powerpoint.presentation.macroEnabled.12';
        $mimes['eps']  = 'application/postscript';
        $mimes['psd']  = 'image/vnd.adobe.photoshop';
        $mimes['ai']   = 'application/postscript';
        $mimes['webp'] = 'image/webp';
        $mimes['indd'] = 'application/x-indesign';
        $mimes['idml'] = 'application/vnd.adobe.indesign-idml-package';
        $mimes['otf']  = 'font/otf';
        $mimes['ttf']  = 'font/ttf';
        $mimes['woff'] = 'font/woff';
        $mimes['woff2'] = 'font/woff2';
        $mimes['json'] = 'application/json';
        $mimes['ics']  = 'text/calendar';
        $mimes['csv']  = 'text/csv';
        $mimes['xml']  = 'application/xml';
        $mimes['webm'] = 'video/webm';
        $mimes['mp4']  = 'video/mp4';
        $mimes['mp3']  = 'audio/mpeg';

        return $mimes;
    }

    /**
     * Allow multiple .dotx mime types.
     *
     * @param array $data
     * @param string $file
     * @param string $filename
     * @param array $mimes
     * @return array
     */
    public function allowMultipleDotxMimeTypes($data, $file, $filename, $mimes)
    {
        $ext = pathinfo($filename, PATHINFO_EXTENSION);

        if (strtolower($ext) === 'dotx') {
            $allowedMimes = [
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
                'application/octet-stream',
                'application/zip',
                'application/msword',
                'application/x-dotx'
            ];

            $realMime = mime_content_type($file);

            if (in_array($realMime, $allowedMimes, true)) {
                return [
                    'ext'  => 'dotx',
                    'type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
                    'proper_filename' => $filename,
                ];
            }
        }

        return $data;
    }

    /**
     * Allow multiple DWG mime types.
     *
     * @param array $data
     * @param string $file
     * @param string $filename
     * @param array $mimes
     * @return array
     */
    public function allowMultipleDwgMimeTypes($data, $file, $filename, $mimes)
    {
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        
        if (strtolower($ext) === 'dwg') {
            $allowedMimes = [
                'image/vnd.dwg',
                'application/acad',
                'application/x-acad',
                'application/autocad_dwg',
                'image/x-dwg',
                'application/dwg',
                'application/x-dwg',
                'application/x-autocad',
                'drawing/dwg'
            ];

            // Get actual MIME type
            $realMime = mime_content_type($file);

            if (in_array($realMime, $allowedMimes, true)) {
                return [
                    'ext'  => 'dwg',
                    'type' => 'image/vnd.dwg', // pick a consistent one for WP
                    'proper_filename' => $filename,
                ];
            }
        }

        return $data;
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
     * Add theme support.
     */
    public static function themeSupport()
    {
        add_theme_support('align-wide');
        add_theme_support('wp-block-styles');
        add_theme_support('editor-styles');
        add_theme_support('menus');
        add_theme_support('post-thumbnails');
        add_theme_support('html5', array(
            'comment-list',
            'comment-form',
            'search-form',
            'gallery',
            'caption',
            'style',
            'script'
        ));

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
        add_theme_support('customize-selective-refresh-widgets');
    }

    /**
     * Removes the generator meta tag from <head> & admin-footer.
     */
    public static function removeTheGenerator()
    {
        add_filter('the_generator', function ($a, $b) {
            return '';
        }, 9, 2);
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
        if (is_admin()) {
            add_filter('option_show_avatars', '__return_false');
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
            $author     = get_query_var('author_name');
            $attachment = get_query_var('attachment');
            $attachment = (empty($attachment)) ? get_query_var('attachment_id') : $attachment;

            if (!empty($author) || !empty($attachment)) {
                $wp_query->set_404();
                $wp_query->is_feed = false;
            }
        }
    }
}
