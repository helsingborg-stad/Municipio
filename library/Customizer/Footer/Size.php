<?php

namespace Municipio\Customizer\Footer;

class Size
{
    public $panel = '';
    public $config = '';
    public $section = 'footer_size';


    public function __construct($footerPanel)
    {
        $this->config = $footerPanel->config;
        $this->panel = $footerPanel->panel;

        add_action('init', array($this, 'addSection'), 9);
        add_action('init', array($this, 'size'), 10);
    }

    public function addSection()
    {
        \Kirki::add_section($this->section, array(
            'title'          => esc_attr__('Size', 'municipio'),
            'description'    => esc_attr__('Footer size options', 'municipio'),
            'panel'          => $this->panel,
            'priority'       => 160,
        ));
    }

    public function size()
    {
        $choices = apply_filters('Municipio/Customizer/Footer/Size', array(
            'default' => __('Default', 'municipio'),
            'c-footer--sm' => __('Small', 'municipio'),
            'c-footer--lg' => __('Large', 'municipio'),
            'c-footer--xl' => __('Extra large', 'municipio')
        ));

        \Kirki::add_field($this->config, array(
            'type'        => 'radio',
            'settings'    => 'footer-size',
            'label'       => __('Footer size', 'municipio'),
            'section'     => $this->section,
            'default'     => 'default',
            'priority'    => 10,
            'multiple'    => 1,
            'choices'     => $choices,
        ));
    }
}
