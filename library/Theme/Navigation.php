<?php

namespace Municipio\Theme;

class Navigation
{
    public function __construct()
    {
        $this->registerMenus();

        if (in_array('mainmenu', (array)get_field('search_display', 'option'))) {
            add_filter('wp_nav_menu_items', array($this, 'addSearchMagnifier'), 10, 2);
            add_filter('Municipio/main_menu/items', array($this, 'addSearchMagnifier'), 10, 2);
        }

        if (!empty(get_field('google_translate_menu', 'option')) && !empty(get_field('show_google_translate', 'option')) && get_field('show_google_translate', 'option') !== 'false') {
            add_filter('wp_nav_menu_items', array($this, 'addTranslate'), 10, 2);
        }
    }

    public function registerMenus()
    {
        $menus = array(
            'help-menu' => __('Help menu', 'municipio'),
            'header-tabs-menu' => __('Header tabs menu', 'municipio')
        );

        if (get_field('nav_primary_enable', 'option') === true) {
            $menus['main-menu'] = __('Main menu', 'municipio');
        }

        if (get_field('nav_sub_enable', 'option') === true) {
            $menus['sidebar-menu'] = __('Sidebar menu', 'municipio');
        }

        register_nav_menus($menus);
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
            $label = '<span data-tooltip="Translate"><i class="fa fa-globe"></i></span>';
        } elseif (get_field('google_translate_show_as', 'option') == 'combined') {
            $label = '<i class="fa fa-globe"></i> Translate';
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
        if ($args && $args->theme_location != apply_filters('Municipio/main_menu_theme_location', 'main-menu')) {
            return $items;
        }

        //Not in child (if inherited from main)
        if ($args && (isset($args->child_menu) && $args->child_menu == true) && $args->theme_location == "main-menu") {
            return $items;
        }

        $search = '<li class="menu-item-search"><a href="#search" class="search-icon-btn toggle-search-top" aria-label="' . __('Search', 'municipio') . '"><span data-tooltip="' . __('Search', 'municipio') . '"><i class="fa fa-search"></i></span></a></li>';

        $items .= $search;
        return $items;
    }

    /**
     * Outputs the html for the breadcrumb
     * @return void
     */
    public static function outputBreadcrumbs()
    {
        global $post;

        $title = get_the_title();
        $output = '';

        echo '<ol class="breadcrumbs" itemscope itemtype="http://schema.org/BreadcrumbList">';

        if (!is_front_page()) {
            if (is_category() || is_single()) {
                echo '<li>';
                the_category('<li>');

                if (is_single()) {
                    echo '<li>';
                    the_title();
                    echo '</li>';
                }
            } elseif (is_page()) {
                if ($post->post_parent) {
                    $anc = get_post_ancestors($post->ID);
                    $title = get_the_title();

                    $int = 1;
                    foreach ($anc as $ancestor) {
                        if (get_post_status($ancestor) != 'private') {
                            $output = '<li itemscope itemprop="itemListElement" itemtype="http://schema.org/ListItem">
                                            <a itemprop="item" href="' . get_permalink($ancestor) . '" title="' . get_the_title($ancestor) . '">
                                                <span itemprop="name">' . get_the_title($ancestor) . '</span>
                                                <meta itemprop="position" content="' . $int . '" />
                                            </a>
                                       </li>' . $output;

                            $int++;
                        }
                    }

                    echo $output;
                    echo '<li itemscope itemprop="itemListElement" itemtype="http://schema.org/ListItem">
                            <span itemprop="name" class="breadcrumbs-current" title="' . $title . '">' . $title . '</span>
                            <meta itemprop="position" content="' . ($int+1) . '" />
                          </li>';
                } else {
                    echo '<li itemscope itemprop="itemListElement" itemtype="http://schema.org/ListItem">
                            <span itemprop="name" class="breadcrumbs-current">' . get_the_title() . '</span>
                            <meta itemprop="position" content="1" />
                          </li>';
                }
            }
        }
        echo '</ol>';
    }
}
