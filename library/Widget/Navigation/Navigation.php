<?php

namespace Municipio\Widget\Navigation;

class Navigation extends \Municipio\Widget\Source\WidgetTemplate
{
    public function setup()
    {
        add_filter('acf/load_field/key=field_5ae6415780335', array($this, 'populateWpMenu'));

        $config = array(
            'id'            => 'municipio-navigation',
            'name'          => 'Navigation',
            'description'   => 'Some description',
            'template'      => 'navigation.navigation',
            'fields'        => array('utilityFields')
        );

        return $config;
    }

    public function viewController($args, $instance)
    {
        $attributes = new \Municipio\Helper\ElementAttribute();
        $attributes->addClass('c-navbar');

        $sizes = array('small' => 'c-navbar--small', 'large' => 'c-navbar--large');

        if ($this->get_field('size') && isset($sizes[$this->get_field('size')])) {
            $attributes->addClass($sizes[$this->get_field('size')]);
        }

        $this->data['navItems'] = $this->navigationItems();
        $this->data['attributes'] = $attributes->outputAttributes();
    }

    /**
     * Prepare links for view
     * @return array | boolean
     */
    public function navigationItems()
    {
        if (!is_array($this->get_field('flexible_navigation')) || empty($this->get_field('flexible_navigation'))) {
            return false;
        }

        //Array key = ACF flexible layout key
        $avalibleLinkTypes = array(
            'wp_menu' => array(
                'template'      => 'widget.navigation.menu',
                'method'        => 'wordpressMenu'
            ),
            'external_link' => array(
                'classes'       => ['c-navbar__action'],
                'template'      => 'widget.navigation.link'
            ),
            'internal_link' => array(
                'classes'       => ['c-navbar__action'],
                'template'      => 'widget.navigation.link'
            ),
            'search_trigger' => array(
                'classes'       => ['o-reset-button', 'u-nowrap', 'c-navbar__action', 'js-search-trigger', 'pricon pricon-search', 'toggle-search-top'],
                'attributes'    => [
                    'onclick' => 'return false;'
                ],
                'text'          =>  __('Search', 'municipio'),
                'template'      => 'widget.navigation.button'
            ),
            'menu_trigger' => array(
                'classes'       => array('c-navbar__action', 'u-nowrap', 'hamburger', 'hamburger--slider','menu-trigger'),
                'attributes'    => [
                    'aria-controls' => 'navigation',
                    'aria-expanded' => 'true/false',
                    'onclick'       => "jQuery(this).toggleClass('is-active');",
                    'data-target'   => '#mobile-menu'
                ],
                'text'          => __("Menu", 'municipio'),
                'template'      =>  'widget.navigation.burger'
            ),
            'translate_trigger' => array(
                'classes'       => ['c-navbar__action', 'js-translate-trigger'],
                'text'          => __('Översätt', 'municipio'),
                'url'           => '#translate',
                'template'      => 'widget.navigation.link'
            )
        );

        $avalibleLinkTypes = apply_filters('Municipio/Widget/Navigation/Navigation/navigationItems/avalibleLinkTypes', $avalibleLinkTypes, $this->data['args'], $this->data['instance']);

        $links = array();

        foreach ($this->get_field('flexible_navigation') as $key => $link) {
            //Bail if ACF layout key doesn't exists within $avalibleLinkTypes
            if (!isset($avalibleLinkTypes[$link['acf_fc_layout']])) {
                continue;
            }

            //Merge repeater data with matching avalibleLinkTypes array
            $links[$key] = array_merge($link, $avalibleLinkTypes[$link['acf_fc_layout']]);

            $attributes = new \Municipio\Helper\ElementAttribute();

            if (isset($links[$key]['attributes']) && !empty($links[$key]['attributes'])) {
               $attributes->addAttribute($links[$key]['attributes']);
            }

            if (isset($links[$key]['classes']) && !empty($links[$key]['classes'])) {
                $attributes->addClass($links[$key]['classes']);
            }

            //Add icon classes if icon exists
            if (isset($links[$key]['icon']) && $links[$key]['icon']) {
                $attributes->addClass('u-nowrap c-navbar__item_link--icon pricon ' .  $links[$key]['icon']);
            }

            $links[$key]['attributes'] = $attributes;

            //Custom method to manipulate item
            if (isset($links[$key]['method']) && method_exists($this, $links[$key]['method'])) {
                $links[$key] = call_user_func(array($this, $links[$key]['method']), $links[$key]);
            }

            if (!isset($links[$key]['url'])) {
                $links[$key]['url'] = '#';
            }

            if (isset($links[$key]['attributes']) && is_object($links[$key]['attributes'])) {
                $links[$key]['attributes'] = $links[$key]['attributes']->outputAttributes();
            }
        }

        $links = apply_filters('Municipio/Widget/Header/Links/MapLinks', $links, $this->data['args'], $this->data['instance']);
        return $links;
    }

    public function wordpressMenu($navItem)
    {
        if (isset($navItem['menu_id']) && is_numeric($navItem['menu_id'])) {

            $menu = \Municipio\Helper\Navigation::wpMenu($navItem['menu_id']);

            foreach ($menu as $key => $link) {
                $menu[$key]->attributes = new \Municipio\Helper\ElementAttribute();

                $menu[$key]->attributes->addClass($link->classes);
                $menu[$key]->attributes->addClass('c-navbar__item');

                if ($link->ID == get_queried_object_id()) {
                    $menu[$key]->attributes->addClass('is-current');
                }

                if ($link->ID == get_post_ancestors(get_queried_object())) {
                    $menu[$key]->attributes->addClass('is-current-ancestor');
                }

                $menu[$key]->attributes = $menu[$key]->attributes->outputAttributes();
            }
        }

        if (isset($menu) && is_array($menu) && !empty($menu)) {
            $navItem['menu'] = $menu;
        }

        return $navItem;
    }

    /**
     * Populate ACF field with WP menus
     * @param array $field ACF fields
     * @return array
     */
    public function populateWpMenu($field)
    {
        $menus = \Municipio\Helper\Navigation::getMenuList();
        $field['choices'] = array();

        foreach ($menus as $menu) {
            $field['choices'][$menu->term_id] = $menu->name . ' (Menu ID: ' . $menu->term_id . ')';
        }

        return $field;
    }

    /**
     * Available methods for BaseWidget and extensions:
     *
     * @method array setup() Used to construct the widget instance. Required return array keys: id, name, description & template
     * @method void viewController($args, $instance) Used to send data to the view
     *
     */
}
