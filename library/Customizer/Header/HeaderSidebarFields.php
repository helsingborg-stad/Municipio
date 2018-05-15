<?php

namespace Municipio\Customizer\Header;

class headerSidebarFields
{
    public $header = array();
    public $config = '';

    public function __construct($header, $config)
    {
        $this->header = $header;
        $this->config = $config;

        $this->headerFields();
    }

    public function headerFields()
    {
        $this->style();
        $this->size();
        $this->border();
        $this->visibility();
        $this->colors();
    }

    public function border()
    {
        $options = array(
            'u-border-top-1' => 'Add top border',
            'u-border-bottom-1' => 'Add bottom border',
        );

        \Kirki::add_field($this->config, array(
            'type'        => 'multicheck',
            'settings'    => 'header-border__' . $this->header['id'],
            'label'       => esc_attr__('Border', 'municipio'),
            'section'     => $this->header['section'],
            'priority'    => 7,
            'choices'     => $options,
        ));
    }

    public function style()
    {
        $choices = apply_filters('Municipio/Customizer/Header/Navbar/Style', array(
            'default' => __('Flat', 'municipio'),
            's-navbar--fill' => __('Fill', 'municipio')
        ));

        \Kirki::add_field($this->config, array(
            'type'        => 'radio-buttonset',
            'settings'    => 'header-style__' . $this->header['id'],
            'label'       => __('Header style', 'textdomain'),
            'section'     => $this->header['section'],
            'default'     => 'default',
            'priority'    => 5,
            'choices'     => $choices,
        ));
    }

    public function size()
    {
        $choices = apply_filters('Municipio/Customizer/Header/Navbar/Size', array(
            'default' => __('Default', 'municipio'),
            'c-navbar--small s-navbar--small' => __('Small', 'municipio'),
            'c-navbar--large s-navbar--large' => __('Large', 'municipio')
        ));

        \Kirki::add_field($this->config, array(
            'type'        => 'radio-buttonset',
            'settings'    => 'header-size__' . $this->header['id'],
            'label'       => __('Header size', 'textdomain'),
            'section'     => $this->header['section'],
            'default'     => 'default',
            'priority'    => 6,
            'choices'     => $choices,
        ));
    }

    public function padding()
    {
        $choices = apply_filters('Municipio/Customizer/Header/Navbar/Padding', array(
            'default' => __('Default', 'municipio'),
            'c-navbar--hard' => __('No padding', 'municipio'),
            'c-navbar--lg' => __('Large', 'municipio')
        ));

        \Kirki::add_field($this->config, array(
            'type'        => 'radio',
            'settings'    => 'header-padding__' . $this->header['id'],
            'label'       => __('Header padding', 'municipio'),
            'section'     => $this->header['section'],
            'default'     => 'default',
            'priority'    => 7,
            'multiple'    => 1,
            'choices'     => $choices,
        ));
    }

    public function visibility()
    {
        $options = array(
            'hidden-xs' => 'Hide XS',
            'hidden-sm' => 'Hide SM',
            'hidden-md' => 'Hide MD',
            'hidden-lg' => 'Hide LG'
        );

        \Kirki::add_field($this->config, array(
            'type'        => 'multicheck',
            'settings'    => 'header-visibility__' . $this->header['id'],
            'label'       => esc_attr__('Visibility settings', 'municipio'),
            'section'     => $this->header['section'],
            'priority'    => 8,
            'choices'     => $options,
        ));
    }

    public function colors()
    {
        \Kirki::add_field($this->config, array(
            'type'        => 'color',
            'settings'    => 'header-color-background__' . $this->header['id'],
            'label'       => esc_attr__('Background color', 'municipio'),
            'section'     => $this->header['section'],
            'priority'    => 1,
            'output' => array(
                array(
                    'element' => '.c-navbar--customizer.customizer-header-' . $this->header['id'],
                    'property' => 'background-color'
                )
            )
        ));

        \Kirki::add_field($this->config, array(
            'type'        => 'color',
            'settings'    => 'header-color-text__' . $this->header['id'],
            'label'       => esc_attr__('Text color', 'municipio'),
            'section'     => $this->header['section'],
            'priority'    => 1,
            'output' => array(
                array(
                    'element' => '.c-navbar--customizer.customizer-header-' . $this->header['id'],
                    'property' => 'color'
                )
            )
        ));

        \Kirki::add_field($this->config, array(
            'type'        => 'color',
            'settings'    => 'header-color-border__' . $this->header['id'],
            'label'       => esc_attr__('Border color', 'municipio'),
            'section'     => $this->header['section'],
            'priority'    => 1,
            'output' => array(
                array(
                    'element' => '.c-navbar--customizer.customizer-header-' . $this->header['id'],
                    'property' => 'border-color'
                )
            )
        ));

        \Kirki::add_field($this->config, array(
            'type'        => 'color',
            'settings'    => 'header-color-link__' . $this->header['id'],
            'label'       => esc_attr__('Link color', 'municipio'),
            'section'     => $this->header['section'],
            'priority'    => 1,
            'output' => array(
                array(
                    'element' => '.c-navbar--customizer.customizer-header-' . $this->header['id'] . ' a, .c-navbar--customizer.customizer-header-' . $this->header['id'] . ' .hamburger-label,
                        .c-navbar--customizer.customizer-header-' . $this->header['id'] . ' .c-nav .c-nav__action',
                    'property' => 'color'
                ),
                array(
                    'element' => '.c-navbar--customizer.customizer-header-' . $this->header['id'] . ' .hamburger-inner, .c-navbar--customizer.customizer-header-' . $this->header['id'] . ' .hamburger-inner::before, .c-navbar--customizer.customizer-header-' . $this->header['id'] . ' .hamburger-inner::after',
                    'property' => 'background'
                )
            )
        ));

        \Kirki::add_field($this->config, array(
            'type'        => 'color',
            'settings'    => 'header-color-link-hover__' . $this->header['id'],
            'label'       => esc_attr__('Link hover color', 'municipio'),
            'section'     => $this->header['section'],
            'priority'    => 1,
            'output' => array(
                array(
                    'element' => '.c-navbar--customizer.customizer-header-' . $this->header['id'] . ' a:hover, .c-navbar--customizer.customizer-header-' . $this->header['id'] . ' .hamburger:hover .hamburger-label,
                        .c-navbar--customizer.customizer-header-' . $this->header['id'] . ' .c-nav .c-nav__action:hover',
                    'property' => 'color'
                ),
                array(
                    'element' => '.c-navbar--customizer.customizer-header-' . $this->header['id'] . ' .hamburger:hover .hamburger-inner, .c-navbar--customizer.customizer-header-' . $this->header['id'] . ' .hamburger:hover .hamburger-inner::before, .c-navbar--customizer.customizer-header-' . $this->header['id'] . ' .hamburger:hover .hamburger-inner::after',
                    'property' => 'background'
                )
            )
        ));
    }
}
