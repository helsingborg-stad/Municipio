<?php

namespace Municipio\Customizer;

class Footer
{
    private static $panelID = 'panel_footer';

    public $avalibleAreas = array();
    public $enabledAreas = array();

    public function __construct()
    {
        add_action('widgets_init', array($this, 'registerWidgetAreas'));
        add_filter('customizer_widgets_section_args', array($this, 'moveWidgetAreas'), 10, 3);
        add_filter('Municipio/Theme/CustomizerFooter/footerClasses', array($this, 'appendFooterVisibility'), 10, 2);

        $this->customizerPanels();
        $this->widgetAreas();
        $this->widgetSettings();
        $this->footerFields();
    }

    public function appendFooterVisibility($classes, $footer)
    {
        if (is_array(get_theme_mod($footer['id'] . '-footer-visibility')) && !empty(get_theme_mod($footer['id'] . '-footer-visibility'))) {
            $classes = array_merge($classes, get_theme_mod($footer['id'] . '-footer-visibility'));
        }

        return $classes;
    }

    /**
     * Get active footers
     * @return array | boolean
     */
    public static function enabledFooters()
    {
        $activeAreas = self::enabledWidgets();

        if (!is_array($activeAreas) || empty($activeAreas)) {
            return false;
        }

        $activePanels = array();

        foreach ($activeAreas as $area) {
            $activePanels[] = $area['position'];
        }

        if (!empty($activePanels)) {
            return array_unique($activePanels);
        }

        return false;
    }

    public static function avaliblefooters()
    {
        $enabledWidgets = self::enabledWidgets();

        if (!is_array($enabledWidgets) || empty($enabledWidgets)) {
            return false;
        }

        $footers = array();

        foreach ($enabledWidgets as $widgetArea) {
            $footers[$widgetArea['position']]['items'][] = $widgetArea;
            $footers[$widgetArea['position']]['classes'] = array();
        }

        $footers = apply_filters('Municipio/Customizer/footer/avaliblefooters', $footers);

        return $footers;
    }

    public function footerVisibility($footer)
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

        $default = (isset($default[$footer])) ? $default[$footer] : array();

        \Kirki::add_field('municipio_config', array(
            'type'        => 'multicheck',
            'settings'    => $footer . '-footer-visibility',
            'label'       => esc_attr__('Visibility settings', 'municipio'),
            'section'     => 'footer_' . $footer,
            'default'     => $default,
            'priority'    => 10,
            'choices'     => $options,
        ));
    }

    public function footerLinkColor($footer)
    {
        $colors = array(
            '#000000',
            '#ffffff'
        );

        $default = self::defaultfooterColors();
        $default = (isset($default[$footer]['link'])) ? $default[$footer]['link'] : '#000000';

        \Kirki::add_field('municipio_config', array(
            'type'        => 'color-palette',
            'settings'    => $footer . '-footer-link-color',
            'label'       => esc_attr__(ucfirst($footer) . ' footer link color', 'municipio'),
            'section'     => 'footer_' . $footer,
            'default'     => $default,
            'choices'     => array(
                'colors' => $colors,
                'style'  => 'round',
            ),
            'output' => array(
                array(
                    'element' => '.c-footer--customizer.c-footer--' . $footer . ' a, .c-footer--customizer.c-footer--' . $footer,
                    'property' => 'color'
                )
            )
        ));
    }

    public function footerBackground($footer)
    {
        $colors = array_merge((array) \Municipio\Helper\Colors::themeColors(), (array) \Municipio\Helper\Colors::neturalColors());
        $default = self::defaultfooterColors();

        $default = (isset($default[$footer]['background'])) ? $default[$footer]['background'] : '#000000';

        \Kirki::add_field('municipio_config', array(
            'type'        => 'color-palette',
            'settings'    => $footer . '-footer-background',
            'label'       => esc_attr__(ucfirst($footer) . ' footer background', 'municipio'),
            'section'     => 'footer_' . $footer,
            'default'     => $default,
            'choices'     => array(
                'colors' => $colors,
                'style'  => 'round',
            ),
            'output' => array(
                array(
                    'element' => '.c-footer--customizer.c-footer--' . $footer,
                    'property' => 'background-color'
                )
            )
        ));
    }

    public static function defaultfooterColors()
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
     * Setup section for each active footer
     * @return void
     */
    public function footerSection($footer)
    {
        \Kirki::add_section('footer_' . $footer, array(
            'title'          => esc_attr__(ucfirst($footer) . ' footer', 'municipio'),
            'panel'          => self::$panelID,
            'priority'       => 30,
        ));
    }

    public function footerFields()
    {
        $footers = self::enabledFooters();

        if (!is_array($footers) || empty($footers)) {
            return;
        }

        foreach ($footers as $footer) {
            $this->footerSection($footer);
            $this->footerBackground($footer);
            $this->footerLinkColor($footer);
            $this->footerVisibility($footer);
        }
    }

    /**
     * Registers new widget areas based on activated widget areas
     * @return void
     */
    public function registerWidgetAreas()
    {
        $avalibleAreas = self::avalibleWidgets();
        $enabledAreas = get_theme_mod('active_footer_widgets');

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
                'description'   => __('Sidebar that sits in the footer, takes up 100% of the widht.', 'municipio'),
                'before_widget' => '<div class="%2$s">',
                'after_widget'  => '</div>',
                'before_title'  => '<h3>',
                'after_title'   => '</h3>'
            ));
        }
    }

    /**
     * Move activaed widget areas to footer widgets panel in customizer
     * @return void
     */
    public function moveWidgetAreas($section_args, $section_id, $sidebar_id)
    {
        if (get_theme_mod('active_footer_widgets') && is_array(get_theme_mod('active_footer_widgets')) && in_array($sidebar_id, get_theme_mod('active_footer_widgets'))) {
            $section_args['panel'] = 'panel_footer_widgets';
        }

        return $section_args;
    }

    public function widgetSettings()
    {
        if (!isset($this->avalibleAreas) || !is_array($this->avalibleAreas) || empty($this->avalibleAreas)) {
            return;
        }

        \Kirki::add_section('footer_widget_settings', array(
            'title'          => esc_attr__('Widget settings', 'municipio'),
            'panel'          => self::$panelID,
            'priority'       => 100,
        ));

        $options = array();

        foreach ($this->avalibleAreas as $widgetArea) {
            $options[$widgetArea['id']] = esc_attr__($widgetArea['name'], 'municipio');
        }

        $defaults = array(
            'primary-footer-left',
            'primary-footer-right'
        );

        $defaults = apply_filters('Municipio/Customizer/Footer/WidgetSettings/Defaults', $defaults);

        \Kirki::add_field('municipio_config', array(
            'type'        => 'multicheck',
            'settings'    => 'active_footer_widgets',
            'label'       => esc_attr__('Footer widget settings', 'municipio'),
            'section'     => 'footer_widget_settings',
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
        \Kirki::add_panel(self::$panelID, array(
            'priority'    => 80,
            'title'       => esc_attr__('Footer', 'municipio'),
            'description' => esc_attr__('Footer settings', 'municipio'),
        ));

        \Kirki::add_panel('panel_footer_widgets', array(
            'priority'    => 80,
            'title'       => esc_attr__('Footer widgets', 'municipio'),
            'description' => esc_attr__('Footer settings', 'municipio'),
        ));
    }

    /**
     * Get activated widget areas
     * @param boolean $mapped Determine if the returned array should be mapped or not (default to true)
     * @return array | boolean
     */
    public static function enabledWidgets($mapped = true)
    {
        if (!is_array(get_theme_mod('active_footer_widgets')) || empty(get_theme_mod('active_footer_widgets') || !is_array(self::avalibleWidgets()) || empty(self::avalibleWidgets()))) {
            return false;
        }

        if (!$mapped) {
            return get_theme_mod('active_footer_widgets');
        }

        $widgets = array();

        foreach (self::avalibleWidgets() as $widgetArea) {
            if (in_array($widgetArea['id'], get_theme_mod('active_footer_widgets'))) {
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
                'id'            => 'top-footer-left',
                'name'          => 'Top footer left',
                'description'   => 'Footer widget area',
                'position'      => 'top',
                'alignment'     => 'left'
            ],
            [
                'id'            => 'top-footer-center',
                'name'          => 'Top footer center',
                'description'   => 'Footer widget area',
                'position'      => 'top',
                'alignment'     => 'center'
            ],
            [
                'id'            => 'top-footer-right',
                'name'          => 'Top footer right',
                'description'   => 'Footer widget area',
                'position'      => 'top',
                'alignment'     => 'right'
            ],
            [
                'id'            => 'primary-footer-left',
                'name'          => 'Primary footer left',
                'description'   => 'Footer widget area',
                'position'      => 'primary',
                'alignment'     => 'left'
            ],
            [
                'id'            => 'primary-footer-center',
                'name'          => 'Primary footer center',
                'description'   => 'Footer widget area',
                'position'      => 'primary',
                'alignment'     => 'center'
            ],
            [
                'id'            => 'primary-footer-right',
                'name'          => 'Primary footer right',
                'description'   => 'Footer widget area',
                'position'      => 'primary',
                'alignment'     => 'right'
            ],
            [
                'id'            => 'bottom-footer-left',
                'name'          => 'Bottom footer left',
                'description'   => 'Footer widget area',
                'position'      => 'bottom',
                'alignment'     => 'left'
            ],
            [
                'id'            => 'bottom-footer-center',
                'name'          => 'Bottom footer center',
                'description'   => 'Footer widget area',
                'position'      => 'bottom',
                'alignment'     => 'center'
            ],
            [
                'id'            => 'bottom-footer-right',
                'name'          => 'Bottom footer right',
                'description'   => 'Footer widget area',
                'position'      => 'bottom',
                'alignment'     => 'right'
            ]
        );

        return apply_filters('Municipio/Customizer/Footer/Widgets/avalibleWidgets', $avalibleWidgets);
    }
}
