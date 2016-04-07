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
        $navAlign = 'justify';
        if (!empty(get_field('nav_primary_align', 'option'))) {
            $navAlign = get_field('nav_primary_align', 'option');
        }

        $depth = 1;
        if (get_field('nav_primariy_dropdown', 'option') === true && intval(get_field('nav_primary_depth', 'option')) > -1) {
            $depth = intval(get_field('nav_primary_depth', 'option'));
        }

        return wp_nav_menu(array(
            'echo' => false,
            'depth' =>  $depth,
            'theme_location' => 'main-menu',
            'container' => false,
            'container_class' => 'menu-{menu-slug}-container',
            'container_id' => '',
            'menu_class' => 'nav nav-' . $navAlign . ' ' . apply_filters('Municipio/desktop_menu_breakpoint', 'hidden-xs hidden-sm'),
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
        $menu = new \Municipio\Helper\NavigationTree(array(
            'include_top_level' => true,
            'render' => get_field('nav_primary_render', 'option'),
            'depth' => get_field('nav_primary_depth', 'option')
        ));

        $navAlign = 'justify';
        if (!empty(get_field('nav_primary_align', 'option'))) {
            $navAlign = get_field('nav_primary_align', 'option');
        }

        if (isset($menu) && $menu->itemCount() > 0) {
            return '<ul class="' . implode(' ', apply_filters('Municipio/main_menu_classes', array('nav', 'nav-' . $navAlign))) . ' ' . apply_filters('Municipio/desktop_menu_breakpoint', 'hidden-xs hidden-sm') . '">' . $menu->render(false) . '</ul>';
        }

        return '';
    }

    /**
     * Get navigation tree mobile menu
     * @return [type] [description]
     */
    public function mobileMenuAuto()
    {
        $mobileMenuArgs = array(
            'include_top_level' => true
        );

        if (get_field('nav_primary_type', 'option') == 'wp') {
            $mobileMenuArgs['top_level_type'] = 'mobile';
        }

        $menu = new \Municipio\Helper\NavigationTree($mobileMenuArgs);

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
                'walker' => new \Municipio\Walker\SidebarMenu()
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
        $menu = new \Municipio\Helper\NavigationTree(array(
            'include_top_level' => !empty(get_field('nav_sub_include_top_level', 'option')) ? get_field('nav_sub_include_top_level', 'option') : false,
            'render' => get_field('nav_sub_render', 'option'),
            'depth' => get_field('nav_sub_depth', 'option')
        ));

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
