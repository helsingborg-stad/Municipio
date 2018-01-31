<?php

namespace Municipio\Customizer;

class Header
{
    const PANEL_ID = 'panel_header';

    public $avalibleAreas = array();
    public $enabledAreas = array();

    public function __construct()
    {
        add_action('widgets_init', array($this, 'registerWidgetAreas'));
        add_filter('customizer_widgets_section_args', array($this, 'moveWidgetAreas'), 10, 3);
        add_filter('Municipio/Theme/Header/headerClasses', array($this, 'appendHeaderVisibility'), 10, 2);
        $this->customizerPanels();
        $this->widgetAreas();
        $this->widgetSettings();
        $this->headerFields();
    }

    public function appendHeaderVisibility($classes, $header)
    {
        if (is_array(get_theme_mod($header['id'] . '-header-visibility')) && !empty(get_theme_mod($header['id'] . '-header-visibility'))) {
            $classes = array_merge($classes, get_theme_mod($header['id'] . '-header-visibility'));
        }

        return $classes;
    }

    /**
     * Get active headers
     * @return array | boolean
     */
    public static function enabledHeaders()
    {
        $activeAreas = self::enabledWidgets();
        $activePanels = array();

        foreach ($activeAreas as $area) {
            $activePanels[] = $area['position'];
        }

        if (!empty($activePanels)) {
            return array_unique($activePanels);
        }

        return false;
    }

    public static function avalibleHeaders()
    {
        $enabledWidgets = self::enabledWidgets();

        if (!is_array($enabledWidgets) || empty($enabledWidgets)) {
            return false;
        }

        $headers = array();

        foreach ($enabledWidgets as $widgetArea) {
            $headers[$widgetArea['position']]['items'][] = $widgetArea;
            $headers[$widgetArea['position']]['classes'] = array();
        }

        $headers = apply_filters('Municipio/Customizer/Header/avalibleHeaders', $headers);

        return $headers;
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

        \Kirki::add_field('municipio_config', array(
            'type'        => 'multicheck',
            'settings'    => $header . '-header-visibility',
            'label'       => esc_attr__('Visibility settings', 'municipio'),
            'section'     => 'header_' . $header,
            'default'     => $default,
            'priority'    => 10,
            'choices'     => $options,
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

        \Kirki::add_field('municipio_config', array(
            'type'        => 'color-palette',
            'settings'    => $header . '-header-link-color',
            'label'       => esc_attr__(ucfirst($header) . ' header link color', 'municipio'),
            'section'     => 'header_' . $header,
            'default'     => $default,
            'choices'     => array(
                'colors' => $colors,
                'style'  => 'round',
            ),
            'output' => array(
                array(
                    'element' => '.c-header--customizer.c-header--' . $header . ' a',
                    'property' => 'color'
                )
            )
        ));
    }

    public function headerBackground($header)
    {
        $colors = array_merge(\Municipio\Helper\Colors::themeColors(), \Municipio\Helper\Colors::neturalColors());
        $default = self::defaultHeaderColors();

        $default = (isset($default[$header]['background'])) ? $default[$header]['background'] : '#000000';

        \Kirki::add_field('municipio_config', array(
            'type'        => 'color-palette',
            'settings'    => $header . '-header-background',
            'label'       => esc_attr__(ucfirst($header) . ' header background', 'municipio'),
            'section'     => 'header_' . $header,
            'default'     => $default,
            'choices'     => array(
                'colors' => $colors,
                'style'  => 'round',
            ),
            'output' => array(
                array(
                    'element' => '.c-header--customizer.c-header--' . $header,
                    'property' => 'background-color'
                )
            )
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

    /**
     * Setup section for each active header
     * @return void
     */
    public function headerSection($header)
    {
        \Kirki::add_section('header_' . $header, array(
            'title'          => esc_attr__(ucfirst($header) . ' header', 'municipio'),
            'panel'          => self::PANEL_ID,
            'priority'       => 20,
        ));
    }

    public function headerFields()
    {
        $headers = self::enabledHeaders();

        if (!is_array($headers) || empty($headers)) {
            return;
        }

        foreach ($headers as $header) {
            $this->headerSection($header);
            $this->headerBackground($header);
            $this->headerLinkColor($header);
            $this->headerVisibility($header);
        }
    }

    /**
     * Registers new widget areas based on activated widget areas
     * @return void
     */
    public function registerWidgetAreas()
    {
        $avalibleAreas = self::avalibleWidgets();
        $enabledAreas = get_theme_mod('active_header_widgets');

        if (!is_array($avalibleAreas) || empty($avalibleAreas) || !is_array($enabledAreas) || empty($enabledAreas)) {
            return;
        }

        $widgetAreas = array();

        foreach ($avalibleAreas as $area) {
            if (in_array($area['id'], $enabledAreas)) {
                $widgetAreas[] = $area;
            }
        }

        if (empty($widgetAreas)) {
            return;
        }

        foreach ($widgetAreas as $area) {
            register_sidebar(array(
                'id'            => $area['id'],
                'name'          => __($area['name'], 'municipio'),
                'description'   => __('Sidebar that sits just before the footer, takes up 100% of the widht.', 'municipio'),
                'before_widget' => '<div class="%2$s">',
                'after_widget'  => '</div>',
                'before_title'  => '<h3>',
                'after_title'   => '</h3>'
            ));
        }
    }

    /**
     * Move activaed widget areas to header widgets panel in customizer
     * @return void
     */
    public function moveWidgetAreas($section_args, $section_id, $sidebar_id)
    {
        if (get_theme_mod('active_header_widgets') && is_array(get_theme_mod('active_header_widgets')) && in_array($sidebar_id, get_theme_mod('active_header_widgets'))) {
            $section_args['panel'] = 'panel_header_widgets';
        }

        return $section_args;
    }

    public function widgetSettings()
    {
        if (!isset($this->avalibleAreas) || !is_array($this->avalibleAreas) || empty($this->avalibleAreas)) {
            return;
        }

        \Kirki::add_section('header_widget_settings', array(
            'title'          => esc_attr__('Widget settings', 'municipio'),
            'panel'          => self::PANEL_ID,
            'priority'       => 100,
        ));

        $options = array();

        foreach ($this->avalibleAreas as $widgetArea) {
            $options[$widgetArea['id']] = esc_attr__($widgetArea['name'], 'municipio');
        }

        $defaults = array(
            'primary-header-left',
            'primary-header-right'
        );

        $defaults = apply_filters('Municipio/Customizer/Header/WidgetSettings/Defaults', $defaults);

        \Kirki::add_field('municipio_config', array(
            'type'        => 'multicheck',
            'settings'    => 'active_header_widgets',
            'label'       => esc_attr__('Widget settings', 'municipio'),
            'section'     => 'header_widget_settings',
            'default'     => $defaults,
            'priority'    => 10,
            'choices'     => $options,
        ));
    }

    public function widgetAreas()
    {
        $this->avalibleAreas = self::avalibleWidgets();
        $this->enabledAreas = self::enabledWidgets();
    }

    /**
     * Setup wrapper for sections
     * @return void
     */
    public function customizerPanels()
    {
        \Kirki::add_panel(self::PANEL_ID, array(
            'priority'    => 80,
            'title'       => esc_attr__('Header', 'municipio'),
            'description' => esc_attr__('Header settings', 'municipio'),
        ));

        \Kirki::add_panel('panel_header_widgets', array(
            'priority'    => 80,
            'title'       => esc_attr__('Header widgets', 'municipio'),
            'description' => esc_attr__('Header settings', 'municipio'),
        ));
    }

    /**
     * Get activated widget areas
     * @param boolean $mapped Determine if the returned array should be mapped or not (default to true)
     * @return array | boolean
     */
    public static function enabledWidgets($mapped = true)
    {
        if (!is_array(get_theme_mod('active_header_widgets')) || empty(get_theme_mod('active_header_widgets') || !is_array(self::avalibleWidgets()) || empty(self::avalibleWidgets()))) {
            return false;
        }

        if (!$mapped) {
            return get_theme_mod('active_header_widgets');
        }

        $widgets = array();

        foreach (self::avalibleWidgets() as $widgetArea) {
            if (in_array($widgetArea['id'], get_theme_mod('active_header_widgets'))) {
                $widgets[] = $widgetArea;
            }
        }

        if (!empty($widgets)) {
            return $widgets;
        }

        return false;
    }

    /**
     * Get avalible widget areas
     * @return array
     */
    public static function avalibleWidgets()
    {
        $avalibleWidgets = array(
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

        return apply_filters('Municipio/Customizer/Header/Widgets/avalibleWidgets', $avalibleWidgets);
    }
}
