<?php

namespace Municipio\Customizer\Header;

class HeaderPanel
{
    public $headers = array();
    public $config = '';
    public $panel = 'panel_header';

    public static $enabledHeaders = array();

    public function __construct($customizerManager)
    {
        $this->config = $customizerManager->config;
        $this->establishHeaders();

        add_action('init', array($this, 'headerPanel'), 9);
        add_action('admin_init', array($this, 'optionalHeaders'));

        new \Municipio\Customizer\Header\Sidebars($this);
        new \Municipio\Customizer\Header\HeaderFields($this);
    }

   /**
     * Setup customizer header panel
     * @return void
     */
    public function headerPanel()
    {
        if (!isset($this->panel) || !is_string($this->panel) || empty($this->panel)) {
            return;
        }

        \Kirki::add_panel($this->panel, array(
            'priority'    => 80,
            'title'       => esc_attr__('Header', 'municipio'),
            'description' => esc_attr__('Header settings', 'municipio'),
        ));
    }

    /**
     * Returns list of avalible headers, use filter to add/remove headers
     *
     * Avalible keys for each header:
     * - id (string) - Required key which will be used when registering sidebars, customizer sections etc
     * - enabled (boolean) - enable the header (this ignores the "optional" option)
     * - optional (boolean) - creates an option in backend to enable the header
     *
     * @return array Avalible headers
     */
    public function avalibleHeaders()
    {
        $avalibleHeaders = array(
            array(
                'id'            => 'top',
                'name'          => 'Top header',
                'optional'      => true
            ),
            array(
                'id'            => 'primary',
                'name'          => 'Primary header',
                'enabled'       => true
            ),
            array(
                'id'            => 'secondary',
                'name'          => 'Secondary header',
                'optional'      => true
            )
        );

        $headers = array();

        foreach ($avalibleHeaders as $header) {
            $headers[] = apply_filters('Municipio/Customizer/Header/HeaderPanel/avalibleHeaders', $header, $header['id']);
        }

        return $headers;
    }

    public function establishHeaders()
    {
        $avalibleHeaders = $this->avalibleHeaders();

        if (!is_array($avalibleHeaders) || empty($avalibleHeaders)) {
            return;
        }

        $enabledHeaders = array();

        //Enable by option
        foreach ($avalibleHeaders as $key => $header) {
            if (isset($header['optional']) && $header['optional'] == true) {
                if (is_array(get_field('customizer_optional_headers', 'options')) && in_array($header['id'], get_field('customizer_optional_headers', 'options'))) {
                    $avalibleHeaders[$key]['enabled'] = true;
                }
            }
        }

        //Map enabled headers
        foreach ($avalibleHeaders as $header) {
            if (isset($header['enabled']) && $header['enabled'] == true && isset($header['id']) && $header['id']) {
                $enabledHeaders[] = $this->mapHeader($header);
            }
        }

        if (is_array($enabledHeaders) && !empty($enabledHeaders)) {
            $this->headers = $enabledHeaders;
            self::$enabledHeaders = $enabledHeaders;

            return true;
        }

        return false;
    }

    public function mapHeader($header)
    {
        if (!isset($header['id']) || !is_string($header['id'])) {
            return;
        }

        $header['id'] = sanitize_title($header['id']);

        //Unset unnecessary vars
        unset($header['enabled']);
        unset($header['optional']);

        //Append sidebar
        $header['name'] = (isset($header['name']) && is_string($header['name'])) ? $header['name'] : ucfirst($header['id']) . ' header';
        $header['description'] = (isset($header['description']) && is_string($header['description'])) ? $header['description'] : 'Sidebar that sits in the header';
        $header['sidebar'] = 'customizer-header-' . $header['id'];
        $header['section'] = 'sidebar-widgets-' . $header['sidebar'];

        return $header;
    }

    public static function getHeaders()
    {
        if (isset(self::$enabledHeaders) && is_array(self::$enabledHeaders) && !empty(self::$enabledHeaders)) {
            return self::$enabledHeaders;
        }

        return false;
    }

    public function optionalHeaders()
    {
        if (!function_exists('acf_add_local_field_group')) {
            return;
        }

        $headers = $this->avalibleHeaders();
        $optionalHeaders = array();

        foreach ($headers as $header) {
            if (!isset($header['optional']) || $header['optional'] == false) {
                continue;
            }

            $optionalHeaders[$header['id']] = (isset($header['name']) && !empty($header['name']) && is_string($header['name'])) ? 'Enable ' . $header['name'] : 'Enable ' . ucfirst($header['id']) . ' header';
        }

        if (!is_array($optionalHeaders) || empty($optionalHeaders)) {
            return;
        }

        acf_add_local_field_group(array(
            'key' => 'group_5acccac61171c',
            'title' => 'Customizer header',
            'fields' => array(
                array(
                    'key' => 'field_5acccad17936d',
                    'label' => 'Optional headers',
                    'name' => 'customizer_optional_headers',
                    'type' => 'checkbox',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'choices' => $optionalHeaders,
                    'allow_custom' => 0,
                    'save_custom' => 0,
                    'default_value' => array(
                    ),
                    'layout' => 'vertical',
                    'toggle' => 0,
                    'return_format' => 'value',
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'options_page',
                        'operator' => '==',
                        'value' => 'acf-options-header',
                    ),
                ),
            ),
            'menu_order' => 0,
            'position' => 'normal',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
            'hide_on_screen' => '',
            'active' => 1,
            'description' => '',
        ));
    }
}
