<?php

namespace Municipio\Customizer\Header;

class UserInterface
{
    public $headers = array();
    public $panel = '';
    public $config;

    public function __construct($customizerHeader)
    {
        $this->headers = $customizerHeader->headers;
        $this->panel = $customizerHeader->panel;
        $this->config = $customizerHeader->config;

        add_action('init', array($this, 'customizerInterface'), 9);
    }

    public function customizerInterface()
    {
        if (!is_array($this->headers) || empty($this->headers)) {
            return;
        }

        //Panel (wrapper of sections)
        $this->customizerPanel();

        foreach ($this->headers as $header) {
            //Section (lives within a panel)
            $this->addSettingsSection($header);

            //Fields (lives within a section)
            $this->headerBackground($header['id']);
            $this->headerLinkColor($header['id']);
            $this->headerVisibility($header['id']);
        }
    }

    /**
     * Setup customizer header panel
     * @return void
     */
    public function customizerPanel()
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

    /**
     * Setup a customizer settings section for each enabled header
     * @return void
     */
    public function addSettingsSection($header)
    {
        if (!isset($header['id']) || !isset($header['name']) || !$this->panel) {
            return;
        }

        \Kirki::add_section('header_' . $header['id'] . '_settings', array(
            'title'          => esc_attr__(ucfirst($header['name']) . ' settings', 'municipio'),
            'panel'          => $this->panel,
            'priority'       => 20,
        ));
    }



    public function headerBackground($header)
    {
        $colors = array_merge((array) \Municipio\Helper\Colors::themeColors(), (array) \Municipio\Helper\Colors::neturalColors());
        $default = self::defaultHeaderColors();

        $default = (isset($default[$header]['background'])) ? $default[$header]['background'] : '#000000';

        \Kirki::add_field($this->config, array(
            'type'        => 'color-palette',
            'settings'    => $header . '-header-background',
            'label'       => esc_attr__(ucfirst($header) . ' header background', 'municipio'),
            'section'     => 'header_' . $header . '_settings',
            'default'     => $default,
            'choices'     => array(
                'colors' => $colors,
                'style'  => 'round',
            ),
            'output' => array(
                array(
                    'element' => '.c-navbar--customizer.c-navbar--' . $header,
                    'property' => 'background-color'
                )
            )
        ));
    }

    public function headerLinkColor($header)
    {
        $colors = array(
            '#000000',
            '#ffffff'
        );

        $default = self::defaultHeaderColors();
        $default = (isset($default[$header]['link'])) ? $default[$header]['link'] : '#000000';

        \Kirki::add_field($this->config, array(
            'type'        => 'color-palette',
            'settings'    => $header . '-header-link-color',
            'label'       => esc_attr__(ucfirst($header) . ' header link color', 'municipio'),
            'section'     => 'header_' . $header . '_settings',
            'default'     => $default,
            'choices'     => array(
                'colors' => $colors,
                'style'  => 'round',
            ),
            'output' => array(
                array(
                    'element' => '.c-navbar--customizer.c-navbar--' . $header . ' a, .c-navbar--customizer.c-navbar--' . $header,
                    'property' => 'color'
                )
            )
        ));
    }

    public function headerVisibility($header)
    {
        $options = array(
            'hidden-xs' => 'Hide XS',
            'hidden-sm' => 'Hide SM',
            'hidden-md' => 'Hide MD',
            'hidden-lg' => 'Hide LG'
        );

        $default = array(
            'top' => array(
                'hidden-xs',
                'hidden-sm'
            ),
            'bottom' => array(
                'hidden-xs',
                'hidden-sm'
            )
        );

        $default = (isset($default[$header])) ? $default[$header] : array();

        \Kirki::add_field($this->config, array(
            'type'        => 'multicheck',
            'settings'    => $header . '-header-visibility',
            'label'       => esc_attr__('Visibility settings', 'municipio'),
            'section'     => 'header_' . $header . '_settings',
            'default'     => $default,
            'priority'    => 10,
            'choices'     => $options,
        ));
    }

    public static function defaultHeaderColors()
    {
        $themeColors = \Municipio\Helper\Colors::themeColors();

        $primary = (isset($themeColors[2])) ? isset($themeColors[2]) : '#000000';

        $colors = array(
            'top' => array(
                'background' => $primary,
                'link' => '#ffffff'
            ),
            'primary' => array(
                'background' => '#ffffff',
                'link' => '#000000'
            ),
            'bottom' => array(
                'background' => $primary,
                'link' => '#ffffff'
            )
        );

        return $colors;
    }
}
