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
        if (get_field('nav_mobile_enable', 'option') === false) {
            return '';
        }

        if (get_field('nav_primary_enable', 'option') === false && get_field('nav_sub_enable', 'option') === false) {
            return '';
        }

        if (get_field('nav_primary_type', 'option') == 'wp' && (!get_field('nav_sub_type', 'option') || in_array(get_field('nav_sub_type', 'option'), array('sub', 'wp')))) {
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

        $args = array(
            'echo' => false,
            'depth' => 1,
            'theme_location' => 'main-menu',
            'container' => false,
            'container_class' => 'menu-{menu-slug}-container',
            'container_id' => '',
            'menu_id' => 'main-menu',
            'before' => '',
            'after' => '',
            'link_before' => '',
            'link_after' => '',
            'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>',
            'fallback_cb' => '__return_false'
        );

        if (get_field('nav_primariy_second_level', 'option')) {
            $classes[] = 'nav-multilevel';
            $args['depth'] = 2;
            $args['walker'] = new \Municipio\Walker\MainMenuSecondary();
            $args['items_section_wrap'] = $args['items_wrap'];
            $args['items_wrap'] = '%3$s';
        }

        $args['menu_class'] = implode(' ', apply_filters('Municipio/main_menu_classes', $classes)) . ' ' . apply_filters('Municipio/desktop_menu_breakpoint', 'hidden-xs hidden-sm');

        return wp_nav_menu($args);
    }

    /**
     * Get navigation tree main menu
     * @return string Markup
     */
    public function mainMenuAuto()
    {
        $markup = null;

        $menu = false;
        $classes = array('nav');

        if (!$menu || !is_string($menu) || (isset($_GET['menu_cache']) && $_GET['menu_cache'] == 'false')) {
            if (!empty(get_field('nav_primary_align', 'option'))) {
                $classes[] = 'nav-' . get_field('nav_primary_align', 'option');
            }

            $menu = new \Municipio\Helper\NavigationTree(array(
                'theme_location' => 'main-menu',
                'include_top_level' => true,
                'render' => get_field('nav_primary_render', 'option'),
                'depth' => 1,
                'sublevel' => get_field('nav_primariy_second_level', 'option'),
                'classes' => implode(' ', apply_filters('Municipio/main_menu_classes', $classes)) . ' ' . apply_filters('Municipio/desktop_menu_breakpoint', 'hidden-xs hidden-sm')
            ));

            if (isset($menu) && $menu->itemCount() > 0) {
                $markup = apply_filters('Municipio/main_menu/items', $menu->render(false));
            }

            return $markup;
        }

        return $menu;
    }

    /**
     * Get navigation tree mobile menu
     * @return [type] [description]
     */
    public function mobileMenuAuto()
    {
        $transientHash = \Municipio\Helper\Hash::short(\Municipio\Helper\Url::getCurrent());

        $transientType = '';
        if (is_user_logged_in()) {
            $transientType = '_loggedin';
        }

        $menu = false; //get_transient('mobile_menu_' . $transientHash . $transientType);

        if (!$menu || (isset($_GET['menu_cache']) && $_GET['menu_cache'] == 'false')) {
            $mobileMenuArgs = array(
                'include_top_level' => true
            );

            if (get_field('nav_primary_type', 'option') == 'wp') {
                $mobileMenuArgs['top_level_type'] = 'mobile';
            }

            $menu = new \Municipio\Helper\NavigationTree($mobileMenuArgs);

            //set_transient('mobile_menu_' . $transientHash . $transientType, $menu, 60*60*168);
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
     * @return string Menu markup
     */
    public function sidebarMenuAuto()
    {
        $menu = false;

        if (!$menu || (isset($_GET['menu_cache']) && $_GET['menu_cache'] == 'false')) {
            $menu = new \Municipio\Helper\NavigationTree(array(
                'include_top_level' => !empty(get_field('nav_sub_include_top_level', 'option')) ? get_field('nav_sub_include_top_level', 'option') : false,
                'render' => get_field('nav_sub_render', 'option'),
                'depth' => get_field('nav_sub_depth', 'option') ? get_field('nav_sub_depth', 'option') : -1,
                'start_depth' => get_field('nav_primariy_second_level', 'option') ? 3 : 1,
                'classes' => 'nav-aside hidden-xs hidden-sm',
                'sidebar' => true
            ));
        }

        if ($menu->itemCount === 0) {
            return '';
        }

        return '<nav id="sidebar-menu">
                    ' . $menu->render(false) . '
                </nav>';
    }

    /**
     * Get menu name by menu location
     * @param mixed $location slug or ID of registered menu
     * @return  string menu name
     */
    public static function getMenuNameByLocation($location)
    {
        if(!has_nav_menu($location)) return false;
        $menus = get_nav_menu_locations();
        $menuTitle = wp_get_nav_menu_object($menus[$location])->name;
        return $menuTitle;
    }
}
