<?php

namespace Municipio\Customizer\Footer;

class FooterFields
{
    public function __construct($footer, $panel, $config)
    {
        $this->footer = $footer;
        $this->config = $config;
        $this->panel = $panel;
        $this->section = 'footer-settings-' . $footer['id'];
        $this->settingsSection();

        add_action('init', array($this, 'fields'), 9);
    }

    public function fields()
    {
        $this->colors();
        $this->size();
    }

    public function colors()
    {
        \Kirki::add_field($this->config, array(
            'type'        => 'color',
            'settings'    => 'footer-color-background-' . $this->footer['id'],
            'label'       => esc_attr__('Background color', 'municipio'),
            'section'     => $this->section,
            'output' => array(
                array(
                    'element' => '.c-footer.c-footer--customizer.c-footer--' . $this->footer['id'],
                    'property' => 'background-color'
                )
            )
        ));

        \Kirki::add_field($this->config, array(
            'type'        => 'color',
            'settings'    => 'footer-color-text-' . $this->footer['id'],
            'label'       => esc_attr__('Text color', 'municipio'),
            'section'     => $this->section,
            'output' => array(
                array(
                    'element' => '.c-footer.c-footer--customizer.c-footer--' . $this->footer['id'],
                    'property' => 'color'
                )
            )
        ));

        \Kirki::add_field($this->config, array(
            'type'        => 'color',
            'settings'    => 'header-color-border-' . $this->footer['id'],
            'label'       => esc_attr__('Border color', 'municipio'),
            'section'     => $this->section,
            'output' => array(
                array(
                    'element' => '.c-footer.c-footer--customizer.c-footer--' . $this->footer['id'],
                    'property' => 'border-color'
                )
            )
        ));

        \Kirki::add_field($this->config, array(
            'type'        => 'color',
            'settings'    => 'header-color-link-' . $this->footer['id'],
            'label'       => esc_attr__('Link color', 'municipio'),
            'section'     => $this->section,
            'output' => array(
                array(
                    'element' => '.c-footer.c-footer--customizer.c-footer--' . $this->footer['id'] . ' a',
                    'property' => 'color'
                )
            )
        ));

        \Kirki::add_field($this->config, array(
            'type'        => 'color',
            'settings'    => 'header-color-link-hover-' . $this->footer['id'],
            'label'       => esc_attr__('Link hover color', 'municipio'),
            'section'     => $this->section,
            'output' => array(
                array(
                    'element' => '.c-footer.c-footer--customizer.c-footer--' . $this->footer['id'] . ' a:hover',
                    'property' => 'color'
                )
            )
        ));
    }

    public function size()
    {
        $choices = apply_filters('Municipio/Customizer/Footer/FooterFields/Size', array(
            'default' => __('Default', 'municipio'),
            'c-footer--sm' => __('Small', 'municipio'),
            'c-footer--lg' => __('Large', 'municipio'),
            'c-footer--xl' => __('Extra large', 'municipio')
        ));

        \Kirki::add_field($this->config, array(
            'type'        => 'radio',
            'settings'    => 'footer-size-' . $this->footer['id'],
            'label'       => __('Footer size', 'municipio'),
            'section'     => $this->section,
            'default'     => 'default',
            'priority'    => 10,
            'multiple'    => 1,
            'choices'     => $choices,
        ));
    }

    public function settingsSection()
    {
        \Kirki::add_section($this->section, array(
            'title'          => esc_attr__($this->footer['name'] . ' settings', 'municipio'),
            'description'    => esc_attr__('Footer settings', 'municipio'),
            'panel'          => $this->panel,
            'priority'       => 1,
        ));
    }
}
