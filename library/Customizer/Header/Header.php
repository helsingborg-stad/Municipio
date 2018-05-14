<?php

namespace Municipio\Customizer\Header;

class Header
{
    public $config = 'municipio_config';
    public $panel = 'panel_header';

    public function __construct($CustomizerManager)
    {
        add_action('init', array($this, 'headerPanel'), 9);
        $this->createHeaders();
    }

    public function createHeaders()
    {
        if (!self::getHeaders() || !is_array(self::getHeaders()) || empty(self::getHeaders())) {
            return;
        }

        foreach (self::getHeaders() as $header) {
            new \Municipio\Customizer\Header\HeaderSidebar($header, $this->panel);
            new \Municipio\Customizer\Header\HeaderSidebarFields($header, $this->config);
        }
    }

    public static function getHeaders()
    {
        $headers = array();

        foreach (get_field('customizer_headers', 'options') as $header) {
            if (!isset($header['id'])) {
                continue;
            }

            $id = self::uniqueKey($header['id'], $headers);
            $sidebarPrefix = 'customizer-header-';
            $sectionPrefix = 'sidebar-widgets-';

            $headers[$id] = array(
                'name'       => $header['name'],
                'id'         => $id,
                'sidebar_id' => $sidebarPrefix . $id,
                'section'    => $sectionPrefix . $sidebarPrefix . $id,
                'description' => (isset($header['description'])) ? $header['description'] : 'Some description',
                'cssClass'   => (isset($header['css_class']) && $header['css_class']) ? $header['css_class'] : false
            );
        }

        return $headers;
    }

    public static function uniqueKey($id, $headers)
    {
        $id = sanitize_title($id);

        if (isset($headers[$id])) {
            $i = 1;

            while (isset($headers[$id . '-' . $i])) {
                $i++;
            }

            return $id . '-' . $i;
        }

        return $id;
    }

    /**
     * Setup Header customizer panel
     * @return void
     */
    public function headerPanel()
    {
        if (!isset($this->panel) || !is_string($this->panel) || empty($this->panel)) {
            return;
        }

        \Kirki::add_panel($this->panel, array(
            'priority'    => 80,
            'title'       => esc_attr__('Header', 'municipio'),
            'description' => esc_attr__('Header settings', 'municipio'),
        ));
    }
}
