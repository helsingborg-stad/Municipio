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
            'template'      => 'header-links.header-links.blade.php'
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

        $avalibleLinkTypes = array(
            'external_link' => array(
                'classes'       => 'c-navbar__item_link c-navbar__item_link--external',
                'attributes'    => ''
            ),
            'internal_link' => array(
                'classes'       => 'c-navbar__item_link c-navbar__item_link--internal',
                'attributes'    => ''
            ),
            'search_trigger' => array(
                'classes'       => 'c-navbar__item_link c-navbar__item_link--search js-search-trigger pricon pricon-search toggle-search-top',
                'attributes'    => 'onclick="return false;"',
                'text'          => 'Search'
            ),
            'menu_trigger' => array(
                'classes'       => 'c-navbar__item_link c-navbar__item_link--menu menu-trigger u-no-decoration u-nowrap',
                'attributes'    => 'onclick="jQuery(this).children(\'.o-burger-icon\').toggleClass(\'is-open\');" data-target="#mobile-menu"',
                'text'          => 'Menu',
                'beforeText'    => '<div class="o-burger-icon"><span></span><span></span><span></span></div>'
            ),
            'translate_trigger' => array(
                'classes'       => 'c-navbar__item_link c-navbar__item_link--translate js-translate-trigger',
                'attributes'    => '',
                'text'          => 'Translate',
                'url'           => '#translate'
            )
        );

        $avalibleLinkTypes = apply_filters('Municipio/Widget/Header/Links/MapLinks/avalibleLinkTypes', $avalibleLinkTypes, $this->data['args'], $this->data['instance']);

        $links = array();
        foreach ($this->get_field('widget_header_links') as $key => $link) {
            //Check if ACF layout key exists within $avalibleLinkTypes
            if (isset($avalibleLinkTypes[$link['acf_fc_layout']])) {
                $links[$key] = array_merge($link, $avalibleLinkTypes[$link['acf_fc_layout']]);

                //Add icon classes if icon exists
                if (isset($link['icon']) && $link['icon']) {
                    $links[$key]['classes'] .= ' c-navbar__item_link--icon pricon ' .  $link['icon'];
                }

                if (!isset($links[$key]['url'])) {
                    $links[$key]['url'] = '#';
                }
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
