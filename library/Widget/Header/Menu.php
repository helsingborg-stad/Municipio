<?php

namespace Municipio\Widget\Header;

class Menu extends \Municipio\Widget\Source\HeaderWidget
{
    public function setup()
    {
        add_filter('acf/load_field/name=widget_header_menu', array($this, 'populateSelectField'));

        $widget = array(
            'id'            => 'menu',
            'name'          => 'Header widget: Menu',
            'description'   => 'Display wordpress or auto generated menu, used in header',
            'template'      => 'header-menu.blade.php'
        );

        return $widget;
    }

    public function viewController($args, $instance)
    {
        if ($this->get_field('widget_header_menu')) {
            $this->data['menu'] = \Municipio\Helper\Navigation::wpMenu($this->get_field('widget_header_menu'));
            $this->data['themeClass'] = apply_filters('Municipio/Widget/Source/HeaderWidget/Menu/ThemeClass', 't-municipio');
        }
    }

    /**
     * Populate ACF field with WP menus
     * @param array $field ACF fields
     * @return array
     */
    public function populateSelectField($field)
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
