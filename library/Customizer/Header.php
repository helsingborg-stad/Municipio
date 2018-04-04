<?php

namespace Municipio\Customizer;

class Header
{
    public static $headers = array();
    public static $panelID = 'panel_header';
    public $sidebars = array();

    public function __construct()
    {
        $this->establishHeaders();

        add_action('widgets_init', array($this, 'registerSidebars'));
        add_action('init', array($this, 'customizerInterface'), 9);
        add_filter('customizer_widgets_section_args', array($this, 'moveSidebars'), 10, 3);
    }

    public function customizerInterface()
    {
        if (!is_array(self::$headers) || empty(self::$headers)) {
            return;
        }

        $this->customizerPanel();

        foreach (self::$headers as $header) {
            //Section
            $this->addCustomizerSection($header);

            //Fields
            $this->headerBackground($header['id']);
            $this->headerLinkColor($header['id']);
            $this->headerVisibility($header['id']);
        }
    }

    public function headerBackground($header)
    {
        $colors = array_merge((array) \Municipio\Helper\Colors::themeColors(), (array) \Municipio\Helper\Colors::neturalColors());
        $default = self::defaultHeaderColors();

        $default = (isset($default[$header]['background'])) ? $default[$header]['background'] : '#000000';

        \Kirki::add_field('municipio_config', array(
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
                    'element' => '.c-header--customizer.c-header--' . $header,
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

        \Kirki::add_field('municipio_config', array(
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
                    'element' => '.c-header--customizer.c-header--' . $header . ' a, .c-header--customizer.c-header--' . $header,
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

        \Kirki::add_field('municipio_config', array(
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

    public static function getHeaders()
    {
        return self::$headers;
    }

    /**
     * Move activaed widget areas to header widgets panel in customizer
     * @return void
     */
    public function moveSidebars($section_args, $section_id, $sidebar_id)
    {
        if (!isset($this->sidebars) || !is_array($this->sidebars) || empty($this->sidebars)) {
            return $section_args;
        }

        if (in_array($sidebar_id, $this->sidebars)) {

            // $section_args['title'] = $section_args['title'] . 'widget';
            $section_args['panel'] = self::$panelID;
        }

        return $section_args;
    }

    /**
     * Setup customizer section section
     * @return void
     */
    public function addCustomizerSection($header)
    {
        \Kirki::add_section('header_' . $header['id'] . '_settings', array(
            'title'          => esc_attr__(ucfirst($header['name']) . ' settings', 'municipio'),
            'panel'          => self::$panelID,
            'priority'       => 20,
        ));
    }

    public function customizerPanel()
    {
        \Kirki::add_panel(self::$panelID, array(
            'priority'    => 80,
            'title'       => esc_attr__('Header', 'municipio'),
            'description' => esc_attr__('Header settings', 'municipio'),
        ));
    }

    public function registerSidebars()
    {
        if (!is_array(self::$headers) || empty(self::$headers)) {
            return;
        }

        foreach (self::$headers as $sidebar) {
            if (!isset($sidebar['id']) || !$sidebar['id'] || !isset($sidebar['name']) || !$sidebar['name']) {
                continue;
            }

            register_sidebar(array(
                'id'            => 'customizer-header-'. $sidebar['id'],
                'name'          => __($sidebar['name'], 'municipio'),
                'description'   => __('Sidebar that sits in the header, takes up 100% of the widht.', 'municipio'),
                'before_widget' => '<div class="%2$s">',
                'after_widget'  => '</div>',
                'before_title'  => '<h3>',
                'after_title'   => '</h3>'
            ));
        }
    }

    public function establishHeaders()
    {
        $avalibleHeaders = array(
            array(
                'id' => 'top',
                'name' => 'Top header',
                'optional' => true
            ),
            array(
                'id' => 'primary',
                'name' => 'Primary header',
                'enabled' => true
            ),
            array(
                'id' => 'secondary',
                'name' => 'Secondary header',
                'optional' => true
            )
        );

        $avalibleHeaders = apply_filters('\Municipio\Customizer\Header\avalibleHeaders', $avalibleHeaders);

        if (!is_array($avalibleHeaders) || empty($avalibleHeaders)) {
            return;
        }

        $enabledHeaders = array();

        //Enable by option
        foreach ($avalibleHeaders as $id => $header) {
            if (isset($header['optional']) && $header['optional'] == true) {
                unset($header['optional']);

                $header['enabled'] = false;
                $avalibleHeaders[$id] = $header;
            }
        }

        //Map enabled headers
        foreach ($avalibleHeaders as $id => $header) {
            if (isset($header['enabled']) && $header['enabled'] == true) {
                unset($header['enabled']);
                $enabledHeaders[$id] = $header;
                $this->sidebars[] = 'customizer-header-' . $header['id'];
            }
        }

        if (is_array($enabledHeaders) && !empty($enabledHeaders)) {
            self::$headers = $enabledHeaders;
            return true;
        }
    }
}
