<?php

namespace Municipio\Theme;

class Navigation
{
    public function __construct()
    {
        $this->registerMenus();

        add_action('after_setup_theme', array($this, 'registerDropDownLinksMenu'));

        if (in_array('mainmenu', (array)get_field('search_display', 'option'))) {
            add_filter('wp_nav_menu_items', array($this, 'addSearchMagnifier'), 10, 2);
            add_filter('Municipio/main_menu/wrapper_end', array($this, 'addSearchMagnifier'), 10, 2);
        }

        if (!empty(get_field('google_translate_menu', 'option')) && !empty(get_field('show_google_translate', 'option')) && get_field('show_google_translate', 'option') !== 'false') {
            add_filter('wp_nav_menu_items', array($this, 'addTranslate'), 10, 2);
        }

        add_action('save_post', array($this, 'purgeTreeMenuTransient'), 10, 2);
        add_filter('the_posts', array($this, 'pageForPostTypeNavigation'));

        add_action('after_setup_theme', array($this, 'submenuAjaxEndpoint'));
    }

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
     * @param  array $posts
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

    public function registerMenus()
    {
        $menus = array(
            'help-menu' => __('Help menu', 'municipio'),
            'header-tabs-menu' => __('Header tabs menu', 'municipio')
        );

        if (get_field('nav_primary_enable', 'option')) {
            $menus['main-menu'] = __('Main menu', 'municipio');
        }

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
        if (get_field('header_dropdown_links', 'option') === true) {
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
            'container' =>  false,
            'menu_class' => 'dropdown-menu',
            'echo' => false
        );

        return wp_nav_menu($args);
    }

    /**
     * Appends translate icon to menu
     * @param  string $items  Items html
     * @param  array  $args   Menu args
     * @return string         Items html
     */
    public function addTranslate($items, $args = null)
    {
        if ($args->theme_location != get_field('google_translate_menu', 'option')) {
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

        $items .= '<li class="menu-item-translate"><a href="#translate" class="translate-icon-btn" aria-label="translate">' . $label . '</a></li>';

        return $items;
    }

    /**
     * Adds a search icon to the main menu
     * @param string $items Menu items html markup
     * @param object $args  Menu args
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

        $search = '<li class="menu-item-search"><a href="#search" class="search-icon-btn toggle-search-top" aria-label="' . __('Search', 'municipio') . '"><span data-tooltip="' . __('Search', 'municipio') . '"><i class="pricon pricon-search"></i></span></a></li>';

        if (isset($args->include_top_level)) {
            $items = $search . $items;
        } else {
            $items .= $search;
        }

        return $items;
    }

    /**
     * Outputs the html for the breadcrumb
     * @return void
     */
    public static function outputBreadcrumbs()
    {
        global $post;

        if (!is_a($post, 'WP_Post')) {
            return;
        }

        $title = get_the_title();
        $post_type = get_post_type_object($post->post_type);
        $output = array();

        echo '<ol class="breadcrumbs" itemscope itemtype="http://schema.org/BreadcrumbList">';

        if (!is_front_page()) {
            $int = 1;
            $output[] = '<li itemscope itemprop="itemListElement" itemtype="http://schema.org/ListItem">
                            <a itemprop="item" href="' . get_home_url() . '" title="' . __('Home') . '">
                            <span itemprop="name">' . __('Home') . '</span><meta itemprop="position" content="' . $int++ . '"></a>
                        </li>';

            if (is_single() && $post_type->has_archive) {
                $cpt_archive_link = (is_string($post_type->has_archive)) ? get_permalink(get_page_by_path($post_type->has_archive)) : get_post_type_archive_link($post_type->name);

                $output[] = '<li itemscope itemprop="itemListElement" itemtype="http://schema.org/ListItem">
                                <a itemprop="item" href="' . $cpt_archive_link . '" title="' .  $post_type->label . '">
                                <span itemprop="name">' .  $post_type->label . '</span><meta itemprop="position" content="' . $int++ . '"></a>
                            </li>';
            }

            if (is_page() || (is_single() && $post_type->hierarchical == true)) {
                if ($post->post_parent) {
                    $anc = array_reverse(get_post_ancestors($post->ID));
                    $title = get_the_title();

                    foreach ($anc as $ancestor) {
                        if (get_post_status($ancestor) != 'private') {
                            $output[] = '<li itemscope itemprop="itemListElement" itemtype="http://schema.org/ListItem">
                                            <a itemprop="item" href="' . get_permalink($ancestor) . '" title="' . get_the_title($ancestor) . '">
                                                <span itemprop="name">' . get_the_title($ancestor) . '</span>
                                                <meta itemprop="position" content="' . $int++ . '" />
                                            </a>
                                       </li>';
                        }
                    }

                    $output[] = '<li itemscope itemprop="itemListElement" itemtype="http://schema.org/ListItem">
                                    <span itemprop="name" class="breadcrumbs-current" title="' . $title . '">' . $title . '</span>
                                    <meta itemprop="position" content="' . ($int++) . '" />
                                </li>';
                } else {
                    $output[] = '<li itemscope itemprop="itemListElement" itemtype="http://schema.org/ListItem">
                                    <span itemprop="name" class="breadcrumbs-current">' . get_the_title() . '</span>
                                    <meta itemprop="position" content="1" />
                                </li>';
                }
            } else {
                if (is_home()) {
                    $title = single_post_title();
                } elseif (is_tax()) {
                    $title = single_cat_title(null, false);
                } elseif (is_category()) {
                    $title = get_the_category();
                } elseif (is_archive()) {
                    $title = post_type_archive_title(null, false);
                } else {
                    $title = get_the_title();
                }
                $output[] = '<li itemscope itemprop="itemListElement" itemtype="http://schema.org/ListItem">
                                <span itemprop="name">' . $title . '</span><meta itemprop="position" content="' . $int++ . '" />
                            </li>';
            }
        }

        $output = apply_filters('Municipio/Breadcrumbs/Items', $output, get_queried_object());

        echo implode("\n", $output);
        echo '</ol>';
    }
}
