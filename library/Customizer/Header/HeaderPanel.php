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
        $this->headerWidgetSettings();
        $this->moveHeaderWidgets();
    }

    /**
     * Setup wrapper for sections
     * @return void
     */
    public function addPanel()
    {
        \Kirki::add_panel(self::PANEL_ID, array(
            'priority'    => 80,
            'title'       => esc_attr__('Header', 'municipio'),
            'description' => esc_attr__('Header settings', 'municipio'),
        ));
    }

    /**
     * Setup section
     * @return void
     */
    public function addSection()
    {
        \Kirki::add_section('header_widget_areas', array(
            'title'          => esc_attr__('Widget areas', 'municipio'),
            'panel'          => self::PANEL_ID,
            'priority'       => 20,
        ));
    }

    /**
     * Move activaed widget areas to header panel in customizer
     * @return void
     */
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

    /**
     * Get avalible widget areas
     * @return array
     */
    public static function avalibleWidgetAreas()
    {
        $avalibleWidgetAreas = array(
            [
                'id'            => 'top-header-left',
                'name'          => 'Top header left',
                'description'   => 'Maecenas sed diam eget risus varius blandit sit amet non magna.',
                'position'      => 'top',
                'alignment'     => 'left'
            ],
            [
                'id'            => 'top-header-center',
                'name'          => 'Top header center',
                'description'   => 'Maecenas sed diam eget risus varius blandit sit amet non magna.',
                'position'      => 'top',
                'alignment'     => 'center'
            ],
            [
                'id'            => 'top-header-right',
                'name'          => 'Top header right',
                'description'   => 'Maecenas sed diam eget risus varius blandit sit amet non magna.',
                'position'      => 'top',
                'alignment'     => 'right'
            ],
            [
                'id'            => 'primary-header-left',
                'name'          => 'Primary header left',
                'description'   => 'Maecenas sed diam eget risus varius blandit sit amet non magna.',
                'position'      => 'primary',
                'alignment'     => 'left'
            ],
            [
                'id'            => 'primary-header-center',
                'name'          => 'Primary header center',
                'description'   => 'Maecenas sed diam eget risus varius blandit sit amet non magna.',
                'position'      => 'primary',
                'alignment'     => 'center'
            ],
            [
                'id'            => 'primary-header-right',
                'name'          => 'Primary header right',
                'description'   => 'Maecenas sed diam eget risus varius blandit sit amet non magna.',
                'position'      => 'primary',
                'alignment'     => 'right'
            ],
            [
                'id'            => 'bottom-header-left',
                'name'          => 'Bottom header left',
                'description'   => 'Maecenas sed diam eget risus varius blandit sit amet non magna.',
                'position'      => 'bottom',
                'alignment'     => 'left'
            ],
            [
                'id'            => 'bottom-header-center',
                'name'          => 'Bottom header center',
                'description'   => 'Maecenas sed diam eget risus varius blandit sit amet non magna.',
                'position'      => 'bottom',
                'alignment'     => 'center'
            ],
            [
                'id'            => 'bottom-header-right',
                'name'          => 'Bottom header right',
                'description'   => 'Maecenas sed diam eget risus varius blandit sit amet non magna.',
                'position'      => 'bottom',
                'alignment'     => 'right'
            ]
        );

        return apply_filters('Municipio/Customizer/Header/HeaderPanel/avalibleWidgetAreas', $avalibleWidgetAreas);
    }

    /**
     * Returns array of widget area id's that will be used as default options
     * @return array
     */
    public static function defaultWidgetAreas()
    {
        $defaults = array(
            'primary-header-left',
            'primary-header-right'
        );

        return apply_filters('Municipio/Customizer/Header/HeaderPanel/defaultWidgetAreas', $defaults);
    }

    /**
     * Appends checkboxes in the customizer for each avalible widget areas
     * @return boolean
     */
    public function headerWidgetSettings()
    {
        if (!is_array(self::avalibleWidgetAreas()) || empty(self::avalibleWidgetAreas())) {
            return false;
        }

        $options = array();

        foreach (self::avalibleWidgetAreas() as $widgetArea) {
            if (isset($widgetArea['id']) && isset($widgetArea['name'])) {
                $options[$widgetArea['id']] = esc_attr__($widgetArea['name'], 'municipio');
            }
        }

        if (is_array($options) && !empty($options)) {
            \Kirki::add_field('municipio_config', array(
                'type'        => 'multicheck',
                'settings'    => 'header_widget_areas_settings',
                'label'       => esc_attr__('Header widget areas', 'municipio'),
                'section'     => 'header_widget_areas',
                'default'     => self::defaultWidgetAreas(),
                'priority'    => 10,
                'choices'     => $options,
            ));


            return true;
        }

        return false;
    }

    /**
     * Registers new widget areas based on activated widget areas
     * @return void
     */
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

    /**
     * Get activated widget areas
     * @param boolean $mapped Determine if the returned array should be mapped or not (default to true)
     * @return array | boolean
     */
    public static function getHeaderWidgetAreas($mapped = true)
    {
        $activeWidgetAreas = get_theme_mod('header_widget_areas_settings');

        if (!is_array($activeWidgetAreas) || empty($activeWidgetAreas)) {
            return false;
        }

        if (!$mapped) {
            return $activeWidgetAreas;
        }

        $avalibleWidgetAreas = self::avalibleWidgetAreas();
        $widgetAreas = array();

        foreach ($avalibleWidgetAreas as $widgetArea) {
            if (in_array($widgetArea['id'], $activeWidgetAreas)) {
                $widgetAreas[] = $widgetArea;
            }
        }

        if (!empty($widgetAreas)) {
            return $widgetAreas;
        }

        return false;
    }
}
