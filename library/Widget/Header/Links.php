<?php

namespace Municipio\Widget\Header;

class Links extends \Municipio\Widget\Source\HeaderWidget
{
    public function setup()
    {
        $widget = array(
            'id'            => 'links',
            'name'          => 'Header widget: Links',
            'description'   => 'Display links with icons or text, used in header',
            'template'      => 'header-links.blade.php'
        );

        return $widget;
    }

    public function viewController($args, $instance)
    {
        $this->data['links'] = $this->mapLinks();
    }

    /**
     * Prepare links for view
     * @return array | boolean
     */
    public function mapLinks()
    {
        if (!is_array($this->get_field('widget_header_links')) || empty($this->get_field('widget_header_links'))) {
            return false;
        }

        $classes = array(
            'external_link'     => 'c-navbar__item--external',
            'internal_link'     => 'c-navbar__item--internal',
            'search_trigger'    => 'c-navbar__item--menu',
            'menu_trigger'      => 'c-navbar__item--trigger',
            'translate_trigger' => 'c-navbar__item--translate'
        );

        $classes = apply_filters('Municipio/Widget/Header/Links/MapLinks/Classes', $classes, $this->data['args'], $this->data['instance']);
        $links = array();


        foreach ($this->get_field('widget_header_links') as $key => $link) {
            $links[$key] = $link;
            $links[$key]['classes'] = "";

            if (isset($classes[$link['acf_fc_layout']])) {
                $links[$key]['classes'] = $classes[$link['acf_fc_layout']];
            }
        }

        $links = apply_filters('Municipio/Widget/Header/Links/MapLinks', $links, $this->data['args'], $this->data['instance']);

        return $links;
    }

    /**
     * Available methods & vars for BaseWidget and extensions:
     *
     * @method array setup() Used to construct the widget instance. Required return array keys: id, name, description & template
     * @method void viewController($args, $instance) Used to send data to the view;
     *
     *
     */
}
