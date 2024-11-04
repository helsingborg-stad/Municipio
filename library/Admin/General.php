<?php

namespace Municipio\Admin;

// use Kirki as Kirki;

class General
{
    public static $url2text = false;

    /**
     * Constructor.
     */
    public function __construct()
    {
        add_filter('wp_dropdown_pages', array($this, 'pageForPostsDropdown'), 10, 3);

        // Post status private rename to "Only for logged in users"
        add_action('current_screen', array($this, 'renamePrivate'));

        add_action('profile_update', function ($userId) {
            // Make sure its a form submission and nothing else
            if (!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'update-user_' . $userId)) {
                return;
            }

            $imageId = get_user_meta($userId, 'user_profile_picture_id', true);

            if (!$imageId) {
                return;
            }

            $imageUrl = wp_get_attachment_image_src($imageId, array(250, 250));
            $imageUrl = isset($imageUrl[0]) ? $imageUrl[0] : null;

            if (!$imageUrl) {
                return;
            }

            update_user_meta($userId, 'user_profile_picture', $imageUrl);
        });

        add_action('add_meta_boxes', array($this, 'removeUnwantedModuleMetaboxes'));

        add_action('admin_footer', array($this, 'setIrisDefaultColorPalette'));
    }

    /**
     * Sets the iris default color palette
     * @return void
     */

    public function setIrisDefaultColorPalette()
    {
        $palettesToGet          = ['color_palette_primary','color_palette_secondary'];
        $colorPalettes          = \Municipio\Helper\Color::getPalettes($palettesToGet);
        $colorPalettesSanitized = array_filter($colorPalettes, fn ($value) => is_array($value) && !empty($value));

        if (!empty($colorPalettesSanitized)) {
            $colorsStr = '';
            foreach ($colorPalettesSanitized as $colors) {
                $colorsStr .= '"' . implode('","', $colors) . '",';
            }
            echo "
            <script>
            if (typeof(acf) != 'undefined') {
                acf.add_filter('color_picker_args', function( args, \$field ){
                    args.palettes = [" . $colorsStr . "]
                    return args;
                });
            }
            </script>
            ";
        }
    }

    /**
     * Removes unwanted metaboxes from the module post types
     * @param  string $postType Post type
     * @return void
     */
    public function removeUnwantedModuleMetaboxes($postType)
    {
        $publicPostTypes   = array_keys(\Municipio\Helper\PostType::getPublic());
        $publicPostTypes[] = 'page';

        if (!in_array($postType, $publicPostTypes)) {
            // Navigation settings
            remove_meta_box('acf-group_56d83cff12bb3', $postType, 'side');

            // Display settings
            remove_meta_box('acf-group_56c33cf1470dc', $postType, 'side');
        }
    }

    /**
     * Renames the "private" post visibility to "only for logged in users"
     * @return void
     */
    public function renamePrivate()
    {
        if (!is_admin()) {
            return;
        }

        if (get_current_screen()->action !== '' && get_current_screen()->action !== 'add') {
            return;
        }

        add_filter('gettext', function ($translation, $text, $domain) {
            if ($text !== 'Private') {
                return $translation;
            }

            return __('Only for logged in users', 'municipio');
        }, 10, 3);
    }

    /**
     * Show private pages in the "page for posts" dropdown
     * @param  string $output Dropdown markup
     * @param  array  $r      Arguments
     * @param  array  $pages  Default pages
     * @return string         New dropdown markup
     */
    public function pageForPostsDropdown($output, $r, $pages)
    {
        if ($r['name'] !== 'page_for_posts') {
            return $output;
        }

        $r['post_status'] = array('publish', 'private');
        $pages            = get_pages($r);

        $class = '';
        if (! empty($r['class'])) {
            $class = " class='" . esc_attr($r['class']) . "'";
        }

        $output = "<select name='" . esc_attr($r['name']) . "'" . $class . " id='" . esc_attr($r['id']) . "'>\n";
        if ($r['show_option_no_change']) {
            $output .= "\t<option value=\"-1\">" . $r['show_option_no_change'] . "</option>\n";
        }

        if ($r['show_option_none']) {
            $output .= "\t<option value=\"" . esc_attr($r['option_none_value']) . '">' . $r['show_option_none'] . "</option>\n";
        }

        add_filter('list_pages', array($this, 'listPagesTitle'), 100, 2);

        $output .= walk_page_dropdown_tree($pages, $r['depth'], $r);

        remove_filter('list_pages', array($this, 'listPagesTitle'), 100);

        $output .= "</select>\n";

        return $output;
    }

    /**
     * Show (private) label in "page for posts" dropdown on private pages
     * @param  string $title Page title
     * @param  object $page  Page object
     * @return string        Modified page title
     */
    public function listPagesTitle($title, $page)
    {
        if ($page->post_status == 'private') {
            return $page->post_title . ' (' . __('Private') . ')';
        }

        return $title;
    }
}
