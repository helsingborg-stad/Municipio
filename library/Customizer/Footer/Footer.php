<?php

namespace Municipio\Customizer\Footer;

class Footer
{
    public $config = 'municipio_config';
    public $panel = 'panel_footer';

    public function __construct($c)
    {
        add_action('init', array($this, 'footerPanel'), 9);
        $this->createFooters();
    }

    public function createFooters()
    {
        if (!self::getFooters() || !is_array(self::getFooters()) || empty(self::getFooters())) {
            return;
        }

        foreach (self::getFooters() as $footer) {
            new \Municipio\Customizer\Footer\FooterFields($footer, $this->panel, $this->config);
            new \Municipio\Customizer\Footer\FooterSidebar($footer, $this->panel);
            new \Municipio\Customizer\Footer\FooterSidebarFields($footer, $this->config);
        }
    }

    public static function getFooters() {
        $footers = array();

        foreach (get_field('customizer_footers', 'options') as $footer) {
            if (!isset($footer['id'])) {
                continue;
            }

            $id = self::uniqueKey($footer['id'], $footers);

            $footers[$id] = array(
                'name'       => $footer['name'],
                'id'         => $id,
                'sidebars'    => self::mapSidebars($footer['columns'], $id, $footer['name']),
                'cssClass'   => (isset($footer['css_class']) && $footer['css_class']) ? $footer['css_class'] : false
            );
        }

        return $footers;
    }

    public static function mapSidebars($columnsTotal, $id, $name)
    {
        $sidebarPrefix = 'customizer-footer-';
        $sectionPrefix = 'sidebar-widgets-';
        $columns = array();

        for ($i = 1; $i <= $columnsTotal; $i++) {
            $columns[] = array(
                'name'        => $name . ' column ' . $i,
                'id'          => $sidebarPrefix . $id . '-column-' . $i,
                'description' => 'Some description',
                'section'     => $sectionPrefix . $sidebarPrefix . $id . '-column-' . $i
            );
        }

        return $columns;
    }

    public function footerPanel()
    {
        if (!isset($this->panel) || !is_string($this->panel) || empty($this->panel)) {
            return;
        }

        \Kirki::add_panel($this->panel, array(
            'priority'    => 80,
            'title'       => esc_attr__('Footer', 'municipio'),
            'description' => esc_attr__('Footer settings', 'municipio'),
        ));
    }

    public static function uniqueKey($id, $variable)
    {
        $id = sanitize_title($id);

        if (isset($variable[$id])) {
            $i = 1;

            while (isset($variable[$id . '-' . $i])) {
                $i++;
            }

            return $id . '-' . $i;
        }

        return $id;
    }
}
