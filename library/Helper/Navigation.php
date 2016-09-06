<?php

namespace Municipio\Helper;

class Navigation
{
    /**
     * Finds out which type of menu to use for the main menu
     * @return mixed
     */
    public function mainMenu()
    {
        if (get_field('nav_primary_enable', 'option') === false) {
            return '';
        }

        switch (get_field('nav_primary_type', 'option')) {
            case 'wp':
                return $this->mainMenuWP();
                break;

            default:
                return $this->mainMenuAuto();
                break;
        }
    }

    /**
     * Finds out which type of menu to use for the sidebar menu
     * @return mixed
     */
    public function sidebarMenu()
    {
        if (get_field('nav_sub_enable', 'option') === false) {
            return '';
        }

        if (get_field('nav_primary_type', 'option') == 'wp' && in_array(get_field('nav_sub_type', 'option'), array('sub', 'wp'))) {
            return $this->sidebarMenuWP();
        } else {
            return $this->sidebarMenuAuto();
        }

        return '';
    }

    /**
     * Get the mobile menu
     * @return string Mobile menu html
     */
    public function mobileMenu()
    {
        if (get_field('nav_primary_enable', 'option') === false && get_field('nav_sub_enable', 'option') === false) {
            return '';
        }

        if (get_field('nav_primary_type', 'option') == 'wp' && in_array(get_field('nav_sub_type', 'option'), array('sub', 'wp'))) {
            return $this->mobileMenuWP();
        } else {
            return $this->mobileMenuAuto();
        }
    }

    /**
     * Get WP main menu
     * @return string Markuo
     */
    public function mainMenuWP()
    {
        $classes = array('nav');

        if (!empty(get_field('nav_primary_align', 'option'))) {
            $classes[] = 'nav-' . get_field('nav_primary_align', 'option');
        }

        $depth = 1;
        if (get_field('nav_primariy_dropdown', 'option') === true && intval(get_field('nav_primary_depth', 'option')) > -1) {
            $depth = intval(get_field('nav_primary_depth', 'option'));
            $classes[] = 'nav-dropdown';
        }

        return wp_nav_menu(array(
            'echo' => false,
            'depth' =>  $depth,
            'theme_location' => 'main-menu',
            'container' => false,
            'container_class' => 'menu-{menu-slug}-container',
            'container_id' => '',
            'menu_class' => implode(' ', apply_filters('Municipio/main_menu_classes', $classes)) . ' ' . apply_filters('Municipio/desktop_menu_breakpoint', 'hidden-xs hidden-sm'),
            'menu_id' => 'main-menu',
            'before' => '',
            'after' => '',
            'link_before' => '',
            'link_after' => '',
            'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>',
            'fallback_cb' => '__return_false'
        ));
    }

    /**
     * Get navigation tree main menu
     * @return string Markup
     */
    public function mainMenuAuto()
    {
        $menu = get_transient('main_menu_' . $_SERVER['REQUEST_URI']);
        $classes = array('nav');

        if (!$menu || (isset($_GET['menu_cache']) && $_GET['menu_cache'] == 'false')) {
            $menu = new \Municipio\Helper\NavigationTree(array(
                'include_top_level' => true,
                'render' => get_field('nav_primary_render', 'option'),
                'depth' => get_field('nav_primary_depth', 'option')
            ));

            if (!empty(get_field('nav_primary_align', 'option'))) {
                $classes[] = 'nav-' . get_field('nav_primary_align', 'option');
            }

            if (get_field('nav_primariy_dropdown', 'option') === true) {
                $classes[] = 'nav-dropdown';
            }

            set_transient('main_menu_' . $_SERVER['REQUEST_URI'], $menu, 60*60*168);
        }

        if (isset($menu) && $menu->itemCount() > 0) {
            $markup = '<ul id="main-menu" class="' . implode(' ', apply_filters('Municipio/main_menu_classes', $classes)) . ' ' . apply_filters('Municipio/desktop_menu_breakpoint', 'hidden-xs hidden-sm') . '">';
            $markup .= apply_filters('Municipio/main_menu/items', $menu->render(false));
            $markup .= '</ul>';

            return $markup;
        }

        return '';
    }

    /**
     * Get navigation tree mobile menu
     * @return [type] [description]
     */
    public function mobileMenuAuto()
    {
        $menu = get_transient('mobile_menu_' . $_SERVER['REQUEST_URI']);

        if (!$menu || (isset($_GET['menu_cache']) && $_GET['menu_cache'] == 'false')) {
            $mobileMenuArgs = array(
                'include_top_level' => true
            );

            if (get_field('nav_primary_type', 'option') == 'wp') {
                $mobileMenuArgs['top_level_type'] = 'mobile';
            }

            $menu = new \Municipio\Helper\NavigationTree($mobileMenuArgs);

            set_transient('mobile_menu_' . $_SERVER['REQUEST_URI'], $menu, 60*60*168);
        }

        if ($menu->itemCount === 0) {
            return '';
        }

        return '<ul class="nav-mobile">' . $menu->render(false) . '</ul>';
    }

    /**
     * Get WP mobile menu
     * @return string Markuo
     */
    public function mobileMenuWP()
    {
        if (get_field('nav_sub_type', 'option') == 'sub') {
            return wp_nav_menu(array(
                'echo' => false,
                'depth' =>  0,
                'theme_location' => 'main-menu',
                'container' => false,
                'container_class' => '',
                'container_id' => '',
                'menu_class' => 'nav-mobile',
                'menu_id' => '',
                'before' => '',
                'after' => '',
                'link_before' => '',
                'link_after' => '',
                'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>',
                'fallback_cb' => '__return_false'
            ));
        }

        return wp_nav_menu(array(
            'echo' => false,
            'depth' =>  0,
            'theme_location' => 'sidebar-menu',
            'container' => false,
            'container_class' => '',
            'container_id' => '',
            'menu_class' => 'nav-mobile',
            'menu_id' => '',
            'before' => '',
            'after' => '',
            'link_before' => '',
            'link_after' => '',
            'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>',
            'fallback_cb' => '__return_false'
        ));
    }

    /**
     * Get WP or sub sidebar menu
     * @return string Markuo
     */
    public function sidebarMenuWP()
    {
        if (get_field('nav_sub_type', 'option') == 'sub') {
            return wp_nav_menu(array(
                'theme_location' => 'main-menu',
                'container' => 'nav',
                'container_class' => 'sidebar-menu',
                'container_id' => 'sidebar-menu',
                'menu_class' => 'nav-aside hidden-xs hidden-sm',
                'menu_id' => '',
                'echo' => false,
                'before' => '',
                'after' => '',
                'link_before' => '',
                'link_after' => '',
                'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>',
                'fallback_cb' => '__return_false',
                'walker' => new \Municipio\Walker\SidebarMenu(),
                'child_menu' => true
            ));
        }

        return wp_nav_menu(array(
            'theme_location' => 'sidebar-menu',
            'container' => 'nav',
            'container_class' => 'sidebar-menu',
            'container_id' => 'sidebar-menu',
            'menu_class' => 'nav-aside hidden-xs hidden-sm',
            'menu_id' => '',
            'echo' => false,
            'before' => '',
            'after' => '',
            'link_before' => '',
            'link_after' => '',
            'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>',
            'fallback_cb' => '__return_false'
        ));
    }

    /**
     * Get navigation tree sidebar menu
     * @return [type] [description]
     */
    public function sidebarMenuAuto()
    {
        $menu = get_transient('sidebar_menu_' . $_SERVER['REQUEST_URI']);

        if (!$menu || (isset($_GET['menu_cache']) && $_GET['menu_cache'] == 'false')) {
            $menu = new \Municipio\Helper\NavigationTree(array(
                'include_top_level' => !empty(get_field('nav_sub_include_top_level', 'option')) ? get_field('nav_sub_include_top_level', 'option') : false,
                'render' => get_field('nav_sub_render', 'option'),
                'depth' => get_field('nav_sub_depth', 'option')
            ));

            set_transient('sidebar_menu_' . $_SERVER['REQUEST_URI'], $menu, 60*60*168);
        }

        if ($menu->itemCount === 0) {
            return '';
        }

        return '<nav id="sidebar-menu">
            <ul class="nav-aside hidden-xs hidden-sm">
                ' . $menu->render(false) . '
            </ul>
        </nav>';
    }
}
