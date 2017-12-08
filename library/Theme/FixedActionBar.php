<?php

namespace Municipio\Theme;

class FixedActionBar
{
    public function __construct()
    {
        add_filter('acf/load_field/name=fab_wp_menu', array($this, 'populateSelectField'));
    }

    /**
     * Populate select field with WP menus
     * @param array $field ACF fields
     * @return array
     */
    public function populateSelectField($field)
    {
        $menus = \Municipio\Helper\Navigation::getMenuList();
        $field['choices'] = array();

        foreach ($menus as $menu) {
            $field['choices'][$menu->term_id] = $menu->name . ' (' . $menu->term_id . ')';
        }

        return $field;
    }

    /**
     * Get fixed action bar
     * @return array/boolean
     */
    public static function getFab()
    {
        if (!get_field('fab_settings', 'options') || get_field('fab_settings', 'options') == 'disabled') {
            return false;
        }

        $fab = array();

        if (get_field('fab_settings', 'options') == 'wp' && get_field('fab_wp_menu', 'options')) {
            $fab['menu'] = self::wpMenu(get_field('fab_wp_menu', 'options'));
        }

        if (self::generateClasses() && isset($fab['menu'])) {
            $fab['classes'] = self::generateClasses();
        }

        if (isset($fab['menu'])) {
            return $fab;
        }

        return false;
    }

    /**
     * Generate fab style classes
     * @return string
     */
    public static function generateClasses()
    {
        $classes = array();
        $classes[] = 'fab--right';

        if (get_field('fab_visabllity', 'options') && is_array(get_field('fab_visabllity', 'options')) && !empty(get_field('fab_visabllity', 'options'))) {
            $classes = array_merge($classes, get_field('fab_visabllity', 'options'));
        }

        if (!empty($classes)) {
            return implode(' ', $classes);
        }

        return false;
    }

    /**
     * Generate WP Menu
     * @param string/int $menuId ID to wordpress menu
     * @return string wp menu markup
     */
    public static function wpMenu($menuId)
    {
        $args = array(
            'menu'          => $menuId,
            'echo'          => false,
            'depth'         => 0,
            'container'     => '',
            'menu_class'    => 'dropdown-menu dropdown-menu-arrow dropdown-menu-arrow-right dropdown-menu--up dropdown-menu--right'
        );

        return wp_nav_menu($args);
    }
}
