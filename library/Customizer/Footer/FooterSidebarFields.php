<?php

namespace Municipio\Customizer\Footer;

class FooterSidebarFields
{
    public function __construct($footer, $config)
    {
        $this->sidebars = $footer['sidebars'];
        $this->config = $config;

        add_action('init', array($this, 'sidebarFields'), 9);
    }

    public function sidebarFields()
    {
        if (!is_array($this->sidebars) || empty($this->sidebars)) {
            return;
        }

        foreach ($this->sidebars as $sidebar) {
            if (!isset($sidebar['section']) || !is_string($sidebar['section']) || empty($sidebar['section'])) {
                continue;
            }

            $this->textAlign($sidebar);
            $this->size($sidebar);
            $this->visibility($sidebar);
        }
    }

    public function textAlign($sidebar)
    {
        $choices = apply_filters('', array(
            'none' => __('None', 'municipio'),
            'text-left' => __('Left', 'municipio'),
            'text-center' => __('Center', 'municipio'),
            'text-right' => __('Right', 'municipio')
        ));

        \Kirki::add_field($this->config, array(
            'type'        => 'radio-buttonset',
            'settings'    => 'footer-column-text-align-' . $sidebar['id'],
            'label'       => __('Text alignment', 'municipio'),
            'section'     => $sidebar['section'],
            'default'     => 'left',
            'priority'    => 1,
            'choices'     => $choices
        ));
    }

    /**
     * Adds select box ([sidebar-id]-column-size-[breakpoint]) for column size to each footer widget section use get_theme_mod (eg. get_theme_mod('footer-1-column-xs'))
     *
     * @return void
     */
    public function size($sidebar)
    {
        $breakpoints = \Municipio\Helper\Css::breakpoints(true);
        foreach ($breakpoints as $breakpoint) {
            $choices = array();
            $default = '';
            $grid = \Municipio\Helper\Css::grid('all', $breakpoint);

            foreach ($grid as $i => $size) {
                $i++;
                $choices[$size] = esc_attr__($i . '/' . count($grid), 'municipio');
            }

            $choices = array_reverse($choices);

            if ($breakpoint != $breakpoints[0]) {
                $default = 'inherit';
                $inherit = array($default => 'Inherit from smaller');
                $choices = array_merge($inherit, $choices);
            } else {
                $default = array_keys($choices)[0];
            }

            \Kirki::add_field($this->config, array(
                'type'        => 'select',
                'settings'    => 'footer-column-size-'. $breakpoint . '-' . $sidebar['id'],
                'label'       => __(strtoupper($breakpoint) . ' column size', 'municipio'),
                'section'     => $sidebar['section'],
                'default'     => $default,
                'priority'    => 10,
                'multiple'    => 1,
                'choices'     => $choices,
            ));
        }
    }

    public function visibility($sidebar)
    {
        if (!is_array(\Municipio\Helper\Css::hidden()) || empty(\Municipio\Helper\Css::hidden())) {
            return;
        }

        $options = array();
        foreach (\Municipio\Helper\Css::hidden() as $screen => $class) {
            $options[$class] = __('Hide at ' . strtoupper($screen), 'municipio');
        }

        $default = array();

        \Kirki::add_field($this->config, array(
            'type'        => 'multicheck',
            'settings'    => 'footer-column-visibility-' . $sidebar['id'],
            'label'       => esc_attr__('Visibility settings', 'municipio'),
            'section'     => $sidebar['section'],
            'default'     => $default,
            'priority'    => 10,
            'choices'     => $options,
        ));
    }
}
