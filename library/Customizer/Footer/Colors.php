<?php

namespace Municipio\Customizer\Footer;

class Colors
{
    public $panel = '';
    public $config = '';
    public $section = 'footer_color';


    public function __construct($footerPanel)
    {
        $this->config = $footerPanel->config;
        $this->panel = $footerPanel->panel;

        add_action('init', array($this, 'addSection'), 9);
        add_action('init', array($this, 'backgroundColor'), 10);
        add_action('init', array($this, 'textColor'), 10);
        add_action('init', array($this, 'linkColor'), 10);

        // add_action('init', array($this, 'backgroundImage'), 10); //TO DO
    }

    public function addSection()
    {
        \Kirki::add_section($this->section, array(
            'title'          => esc_attr__('Color', 'municipio'),
            'description'    => esc_attr__('Footer color options', 'municipio'),
            'panel'          => $this->panel,
            'priority'       => 160,
        ));
    }

    public function backgroundColor()
    {
        $colors = array_merge((array) \Municipio\Helper\Colors::themeColors(), (array) \Municipio\Helper\Colors::neturalColors());
        $default = $colors[0];

        \Kirki::add_field($this->config, array(
            'type'        => 'color-palette',
            'settings'    => 'footer-background',
            'label'       => esc_attr__('Background color', 'municipio'),
            'section'     => $this->section,
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

    public function textColor()
    {
        $colors = array_merge((array) \Municipio\Helper\Colors::themeColors(), (array) \Municipio\Helper\Colors::neturalColors());
        $default = $colors[0];

        \Kirki::add_field($this->config, array(
            'type'        => 'color-palette',
            'settings'    => 'footer-color',
            'label'       => esc_attr__('Text color', 'municipio'),
            'section'     => $this->section,
            'default'     => $default,
            'choices'     => array(
                'colors' => $colors,
                'style'  => 'round',
            ),
            'output' => array(
                array(
                    'element' => '.c-footer.c-footer--customizer',
                    'property' => 'color'
                )
            )
        ));
    }

    public function linkColor()
    {
        $colors = array_merge((array) \Municipio\Helper\Colors::themeColors(), (array) \Municipio\Helper\Colors::neturalColors());
        $default = $colors[0];

        \Kirki::add_field($this->config, array(
            'type'        => 'color-palette',
            'settings'    => 'footer-link-color',
            'label'       => esc_attr__('Link color', 'municipio'),
            'section'     => $this->section,
            'default'     => $default,
            'choices'     => array(
                'colors' => $colors,
                'style'  => 'round',
            ),
            'output' => array(
                array(
                    'element' => '.c-footer.c-footer--customizer a',
                    'property' => 'color'
                )
            )
        ));
    }
}
