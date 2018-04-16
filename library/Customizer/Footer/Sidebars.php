<?php

namespace Municipio\Customizer\Footer;

class Sidebars
{
    public function __construct($CustomizerFooter)
    {
        $this->sidebars = $CustomizerFooter->sidebars;
        $this->panel = $CustomizerFooter->panel;

        add_action('widgets_init', array($this, 'registerSidebars'));
        add_filter('customizer_widgets_section_args', array($this, 'moveSidebars'), 10, 3);
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
            $this->columnSize($sidebar);
            $this->visibilityField($sidebar);
        }
    }

    public function textAlign($sidebar)
    {
        $choices = apply_filters('', array(
            'text-left' => __('Left', 'municipio'),
            'text-center' => __('Center', 'municipio'),
            'text-right' => __('Right', 'municipio')
        ));

        \Kirki::add_field($this->config, array(
            'type'        => 'radio-buttonset',
            'settings'    => $sidebar['id'] . '-text-align',
            'label'       => __('Text alignment', 'municipio'),
            'section'     => $sidebar['section'],
            'default'     => 'left',
            'priority'    => 10,
            'choices'     => $choices
        ));
    }

    /**
     * Adds select box ([sidebar-id]-column-size-[breakpoint]) for column size to each footer widget section use get_theme_mod (eg. get_theme_mod('footer-1-column-xs'))
     *
     * @return void
     */
    public function columnSize($sidebar)
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
                'settings'    => $sidebar['id'] . '-column-' . $breakpoint,
                'label'       => __(strtoupper($breakpoint) . ' column size', 'municipio'),
                'section'     => $sidebar['section'],
                'default'     => $default,
                'priority'    => 10,
                'multiple'    => 1,
                'choices'     => $choices,
            ));
        }
    }

    public function visibilityField($sidebar)
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
            'settings'    => $sidebar['id'] . '-visibility',
            'label'       => esc_attr__('Visibility settings', 'municipio'),
            'section'     => $sidebar['section'],
            'default'     => $default,
            'priority'    => 10,
            'choices'     => $options,
        ));
    }

    /**
     * Move footer sidebars to Footer panel (within the customizer)
     * @return void
     */
    public function moveSidebars($section_args, $section_id, $sidebar_id)
    {
        if (!isset($this->sidebars) || !is_array($this->sidebars) || empty($this->sidebars) || !is_string($this->panel) || empty($this->panel)) {
            return $section_args;
        }

        $sidebars = array();

        foreach ($this->sidebars as $sidebar) {
            if (isset($sidebar['id'])) {
                $sidebars[] = $sidebar['id'];
            }
        }

        if (in_array($sidebar_id, $sidebars)) {
            $section_args['panel'] = $this->panel;
        }

        return $section_args;
    }

    public function registerSidebars()
    {
        if (!is_array($this->sidebars) || empty($this->sidebars)) {
            return;
        }

        foreach ($this->sidebars as $sidebar) {
            if (!isset($sidebar['id']) || !$sidebar['id'] || !isset($sidebar['name']) || !$sidebar['name'] || !isset($sidebar['description']) || !$sidebar['description']) {
                continue;
            }

            register_sidebar(apply_filters('Municipio/Customizer/Footer/CustomizerFooter/RegisterSidebars', array(
                'id'            => $sidebar['id'],
                'name'          => __($sidebar['name'], 'municipio'),
                'description'   => __($sidebar['description'], 'municipio'),
                'before_widget' => '<div class="widget %2$s">',
                'after_widget'  => '</div>',
                'before_title'  => '<h3>',
                'after_title'   => '</h3>'
            ), $sidebar));
        }
    }
}
