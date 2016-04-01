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

        if (isset($menu) && $menu->itemCount() > 0) {
            return $menu;
        }

        return '';
    }

    /**
     * Get the mobile menu
     * @return string Mobile menu html
     */
    public function mobileMenu()
    {
        $menu = new \Municipio\Helper\NavigationTree(array(
            'include_top_level' => true
        ));

        if ($menu->itemCount === 0) {
            return '';
        }

        return $menu->render(false);
    }
}
