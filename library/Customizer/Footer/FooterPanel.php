<?php

namespace Municipio\Customizer\Footer;

class FooterPanel
{
    public $config = '';
    public $panel = 'panel_footer';

    public static $avalibleSidebars = array();

    public $sidebars = array();

    public function __construct($customizerManager)
    {
        $this->config = $customizerManager->config;

        $this->establishSidebars();

        add_action('init', array($this, 'footerPanel'), 9);

        new \Municipio\Customizer\Footer\Colors($this);
        new \Municipio\Customizer\Footer\Sidebars($this);
        new \Municipio\Customizer\Footer\Size($this);
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

    public function footerFields()
    {
        $colors = array_merge((array) \Municipio\Helper\Colors::themeColors(), (array) \Municipio\Helper\Colors::neturalColors());
        $default = array();

        \Kirki::add_field($this->config, array(
            'type'        => 'color-palette',
            'settings'    => 'footer-background',
            'label'       => __('Footer background', 'municipio'),
            'section'     => $this->sections['settings'],
            'default'     => $default,
            'choices'     => array(
                'colors' => $colors,
                'style'  => 'round',
            ),
            'output' => array(
                array(
                    'element' => '.c-footer.c-footer--customizer',
                    'property' => 'background-color'
                )
            )
        ));
    }

    public function footerSettings()
    {
        if (!isset($this->sections['settings']) || !is_string($this->sections['settings']) || empty($this->sections['settings'])) {
            return;
        }

        \Kirki::add_section($this->sections['settings'], array(
            'title'          => esc_attr__('Settings', 'municipio'),
            'description'    => esc_attr__('Footer settings', 'municipio'),
            'panel'          => $this->panel,
            'priority'       => 160,
        ));
    }

    public function establishSidebars()
    {
        $sidebars = array();

        if (get_field('customizer_footer_sidebars', 'options') && get_field('customizer_footer_sidebars', 'options') > 0) {
            for ($i = 1; $i <= get_field('customizer_footer_sidebars', 'options'); $i++) {

                $id = 'customizer-footer-' . $i;
                $sidebars[] = array(
                    'id' => $id,
                    'name' => 'Footer column ' . $i,
                    'description' => 'Footer column ' . $i,
                    'section' => 'sidebar-widgets-' . $id
                );
            }
        }

        $this->sidebars = $sidebars;
        self::$avalibleSidebars = $sidebars;
    }

    public static function getSidebars()
    {
        if (isset(self::$avalibleSidebars) && is_array(self::$avalibleSidebars) && !empty(self::$avalibleSidebars)) {
            return self::$avalibleSidebars;
        }

        return false;
    }
}
