<?php

namespace Municipio\Theme;

/**
 * Class Navigation
 * @package Municipio\Theme
 */
class Navigation
{

    /**
     * Navigation constructor.
     */
    public function __construct()
    {
        $this->registerMenus();

        add_action('after_setup_theme', array($this, 'registerDropDownLinksMenu'));
        add_action('save_post', array($this, 'purgeTreeMenuTransient'), 10, 2); //TODO: Do we need this?
        add_filter('the_posts', array($this, 'pageForPostTypeNavigation')); //TODO: Move to addon plugin if needed

        add_action('after_setup_theme', array($this, 'submenuAjaxEndpoint')); //TODO: Do we need this? 
    }

    /**
     * SubmenuAjax
     */
    public function submenuAjaxEndpoint()
    {
        if (!isset($_GET['load-submenu-id']) || !is_numeric($_GET['load-submenu-id'])) {
            return;
        }

        $submenu = new \Municipio\Helper\NavigationTree(
            array(
                'include_top_level' => false,
                'depth' => 2,
                'wrapper' => '%3$s'
            ),
            $_GET['load-submenu-id']
        );

        wp_send_json('<ul class="sub-menu">' . $submenu->render(false) . '</ul>');
        exit;
    }

    /**
     * Fix sidebar nav if "page for post type" is same as the curretn post's post type
     * @param array $posts
     * @return array
     */
    public function pageForPostTypeNavigation($posts)
    {
        if (is_main_query() && is_single() && isset($posts[0])) {
            $postType = $posts[0]->post_type;
            $parent = get_option("page_for_{$postType}");

            if ($parent) {
                $posts[0]->post_parent = $parent;
            }
        }

        return $posts;
    }

    /**
     * Find out which pages menus must be purged.
     * @param int $postId The post id to empty for
     */
    public function purgeTreeMenuTransient($postId, $post)
    {
        $this->purgeTreeMenuTransientForAncestors($post->post_parent);
    }

    /**
     * Delete tree menu transient for ancestors of post id.
     * @param int $postId The post id
     * @return void
     */
    public function purgeTreeMenuTransientForAncestors($postId)
    {
        // Get page ancestors
        $ancestors = get_post_ancestors($postId);
        $ancestors[] = $postId;

        // Remove front page from ancestors array
        $ancestors = array_reverse($ancestors);

        if ($ancestors[0] == get_option('page_on_front')) {
            unset($ancestors[0]);
        }

        $ancestors = array_values($ancestors);

        // Delete transient for page ancestors
        foreach ($ancestors as $postId) {
            $children = get_children(array(
                'post_parent' => $postId,
                'numberofposts' => -1,
                'post_type' => 'page',
            ));

            foreach ($children as $child) {
                delete_transient('main_menu_' . $child->ID);
                delete_transient('mobile_menu_' . $child->ID);
                delete_transient('sidebar_menu_' . $child->ID);

                delete_transient('main_menu_' . $child->ID . '_loggedin');
                delete_transient('mobile_menu_' . $child->ID . '_loggedin');
                delete_transient('sidebar_menu_' . $child->ID . '_loggedin');
            }
        }
    }

    /**
     * Register Menus
     */
    public function registerMenus()
    {
        $menus = array(
            'help-menu' => __('Help menu', 'municipio'),
            'header-tabs-menu' => __('Header tabs menu', 'municipio')
        );

        if (get_field('nav_primary_enable', 'option')) {
            $menus['main-menu'] = __('Primary menu', 'municipio');
        }

        $menus['secondary-menu'] = __('Secondary menu', 'municipio');

        if (get_field('nav_sub_enable', 'option')) {
            $menus['sidebar-menu'] = __('Sidebar menu', 'municipio');
        }

        register_nav_menus($menus);
    }

    /**
     * Register dropdown links menu
     * @return void
     */
    public function registerDropDownLinksMenu()
    {
        if (get_field('header_dropdown_links', 'option') == true) {
            register_nav_menu('dropdown-links-menu', __('Dropdown Links', 'municipio'));
        }
    }

    /**
     * Output dropdown links menu markup
     * @return string menu markup
     */
    public static function outputDropdownLinksMenu()
    {
        if (!\Municipio\Helper\Navigation::getMenuNameByLocation('dropdown-links-menu')) {
            return;
        }

        $args = array(
            'menu' => \Municipio\Helper\Navigation::getMenuNameByLocation('dropdown-links-menu'),
            'container' => false,
            'menu_class' => 'o-dropdown-links',
            'echo' => false
        );

        return wp_nav_menu($args);
    }

    /**
     * Appends translate icon to menu
     * @param string $items Items html
     * @param array $args Menu args
     * @return string  Items html
     */
    public function addTranslate($items, $args = null)
    {
        if (!is_object($args)) {
            $args = (object)$args;
        }

        if ($args && $args->theme_location != get_field('google_translate_menu', 'option')) {
            return $items;
        }

        //Not in child (if inherited from main)
        if ($args && (isset($args->child_menu) && $args->child_menu == true) && $args->theme_location == "main-menu") {
            return $items;
        }

        $label = 'Translate';
        if (get_field('google_translate_show_as', 'option') == 'icon') {
            $label = '<span data-tooltip="Translate"><i class="pricon pricon-globe"></i></span>';
        } elseif (get_field('google_translate_show_as', 'option') == 'combined') {
            $label = '<i class="pricon pricon-globe"></i> Translate';
        }

        if (isset($_GET['translate']) && !empty($_GET['translate'])) {
            $transQuery = $_GET['translate'];
        } else {
            $transQuery = 'true';
        }

        $translate = '<li class="menu-item-translate"><a href="?translate=' . $transQuery . '#translate" class="translate-icon-btn" aria-label="translate">' . $label . '</a></li>';

        if (isset($args->include_top_level)) {
            $items = $translate . $items;
        } else {
            $items .= $translate;
        }

        return $items;
    }

    /**
     * Adds a search icon to the main menu
     * @param $items Menu items html markup
     * @param null $args Menu args
     * @return string
     */
    public function addSearchMagnifier($items, $args = null)
    {
        if (!is_object($args)) {
            $args = (object)$args;
        }

        if ($args && $args->theme_location != apply_filters('Municipio/main_menu_theme_location', 'main-menu')) {
            return $items;
        }

        //Not in child (if inherited from main)
        if ($args && (isset($args->child_menu) && $args->child_menu == true) && $args->theme_location == "main-menu") {
            return $items;
        }

        $search = '<li class="menu-item-search"><a href="#search" class="search-icon-btn toggle-search-top" aria-label="' . __('Search',
                'municipio') . '"><span data-tooltip="' . __('Search',
                'municipio') . '"><i class="pricon pricon-search"></i></span></a></li>';

        if (isset($args->include_top_level)) {
            $items = $search . $items;
        } else {
            $items .= $search;
        }

        return $items;
    }
}
