<?php

namespace Municipio\Customizer\Header;

class HeaderPanel
{
    const PANEL_ID = 'panel_header';

    public function __construct()
    {
        add_action('widgets_init', array($this, 'registerWidgetAreas'));

        $this->addPanel();
        $this->addSection();
        $this->headerWidgetAreas();
        $this->moveHeaderWidgets();
    }

    public function addPanel()
    {
        \Kirki::add_panel(self::PANEL_ID, array(
            'priority'    => 80,
            'title'       => esc_attr__('Header', 'municipio'),
            'description' => esc_attr__('Header settings', 'municipio'),
        ));
    }

    public function addSection()
    {
        \Kirki::add_section('header_widget_areas', array(
            'title'          => esc_attr__('Widget areas', 'municipio'),
            'panel'          => self::PANEL_ID,
            'priority'       => 20,
        ));
    }

    public function moveHeaderWidgets()
    {
        add_filter('customizer_widgets_section_args', function ($section_args, $section_id, $sidebar_id) {
            if (self::getHeaderWidgetAreas(false) &&
                is_array(self::getHeaderWidgetAreas(false)) &&
                in_array($sidebar_id, self::getHeaderWidgetAreas(false))) {
                $section_args['panel'] = self::PANEL_ID;
            }

            return $section_args;
        }, 10, 3);
    }

    public function headerWidgetAreas()
    {
        $defaults = apply_filters('Municipio/Customizer/Header/HeaderPanel/headerWidgetAreas/Defaults', array(
            'navbar-left',
            'navbar-right'
        ));

        $headerWidgetAreas = apply_filters('Municipio/Customizer/Header/HeaderPanel/headerWidgetAreas', array(
            'top-header-left' => esc_attr__('Top header left', 'municipio'),
            'top-header-center' => esc_attr__('Top header center', 'municipio'),
            'top-header-right' => esc_attr__('Top header right', 'municipio'),
            'primary-header-left' => esc_attr__('Primary header left', 'municipio'),
            'primary-header-center' => esc_attr__('Primary header center', 'municipio'),
            'primary-header-right' => esc_attr__('Primary header right', 'municipio'),
            'bottom-header-left' => esc_attr__('Bottom header left', 'municipio'),
            'bottom-header-center' => esc_attr__('Bottom header center', 'municipio'),
            'bottom-header-right' => esc_attr__('Bottom header right', 'municipio')
        ));

        \Kirki::add_field('municipio_config', array(
            'type'        => 'multicheck',
            'settings'    => 'header_widget_areas_settings',
            'label'       => esc_attr__('Header widget areas', 'municipio'),
            'section'     => 'header_widget_areas',
            'default'     => $defaults,
            'priority'    => 10,
            'choices'     => $headerWidgetAreas,
        ));
    }

    public function registerWidgetAreas()
    {
        $navbars = self::getHeaderWidgetAreas();
        $navbars = apply_filters('Municipio/Customizer/Header/HeaderPanel/registerWidgetAreas', $navbars);

        if ($navbars && is_array($navbars) && !empty($navbars)) {
            foreach ($navbars as $navbar) {
                register_sidebar(array(
                    'id'            => $navbar['id'],
                    'name'          => __($navbar['name'], 'municipio'),
                    'description'   => __('Sidebar that sits just before the footer, takes up 100% of the widht.', 'municipio'),
                    'before_widget' => '<div class="%2$s">',
                    'after_widget'  => '</div>',
                    'before_title'  => '<h3>',
                    'after_title'   => '</h3>'
                ));
            }
        }
    }

    public static function getHeaderWidgetAreas($formatted = true)
    {
        $bars = get_theme_mod('header_widget_areas_settings');
        $navbars = array();

        if (!$formatted) {
            return $bars;
        }

        if (is_array($bars) && !empty($bars)) {
            foreach ($bars as $bar) {
                $propeties = explode('-', $bar);

                if (count($propeties) == 3) {
                    $navbars[] = array(
                        'id' => $bar,
                        'name' => ucfirst($propeties[0]) . ' ' . ucfirst($propeties[1]) . ' ' . ucfirst($propeties[2]),
                        'alignment' => $propeties[2],
                        'position' => $propeties[0]
                    );
                }
            }
        }

        if (!empty($navbars)) {
            return $navbars;
        }

        return false;
    }
}
