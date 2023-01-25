<?php

namespace Municipio\Customizer\Sections;

class LoadDesign
{
    public const SECTION_ID         = "municipio_customizer_section_designlib";
    private const API_URL           = 'https://customizer.helsingborg.io/';
    private const LOAD_DESIGN_KEY   = 'load_design';

    private $uniqueId               = null;

    private $apiActions = [
        'post'  =>  "",
        'single' => 'id' . DIRECTORY_SEPARATOR
    ];

    public function __construct($panelID)
    {

        if (defined('MUNICIPIO_DISABLE_DESIGNSHARE') && MUNICIPIO_DISABLE_DESIGNSHARE === true) {
            return;
        }

        $this->uniqueId = uniqid();

        \Kirki::add_section(self::SECTION_ID, array(
            'title'       => esc_html__('Load a design', 'municipio'),
            'description' => esc_html__('Want a new fresh design to your site? Use one of the options below to serve as a boilerplate!', 'municipio'),
            'panel'          => $panelID,
            'priority'       => 160,
        ));

        //Example controller variable
        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'select',
            'settings'    => self::LOAD_DESIGN_KEY,
            'label'       => esc_html__('Select a design', 'municipio'),
            'section'     => self::SECTION_ID,
            'default'     => false,
            'priority'    => 10,
            'choices'     => $this->loadOptions(),
            'transport'    => 'postMessage'
        ]);

        //Always reset option of theme
        add_filter('theme_mod_' . self::LOAD_DESIGN_KEY, function ($value) {
            return null;
        });

        //Store on save
        add_action('customize_save_after', array($this, 'storeThemeMod'));

        //Cron action to trigger
        add_action('municipio_store_theme_mod', array($this, 'storeThemeMod'));

        //Cron to update design periodically
        add_action('admin_init', function () {
            if (!wp_next_scheduled('municipio_store_theme_mod')) {
                wp_schedule_event(time(), 'daily', 'municipio_store_theme_mod');
            }
        });
    }

    /**
     * Load options of designs
     *
     * @return array
     */
    private function loadOptions(): array
    {

        //Do not load option in frontend applications
        if (!is_customize_preview()) {
            return array();
        }

        $data = wp_remote_get(self::API_URL, [
            'cacheBust' => $this->uniqueId
        ]);

        if (wp_remote_retrieve_response_code($data) == 200) {
            $data = json_decode($data['body']);

            //Reset select
            $choices = [null => __('Select a design', 'municipio')];

            //Populate select
            if (is_array($data) && !empty($data)) {
                foreach ($data as $choice) {
                    $choices[$choice->id] = $choice->name;
                }
            }
        } else {
            $choices['error'] = __("Error loading options", 'municipio');
        }

        return $choices;
    }

    /**
     * Requests to store the theme mod in api
     *
     * @param Object|null $customizerManager
     * @return bool|WP_Error
     */
    public function storeThemeMod($customizerManager = null)
    {
        $response = wp_remote_post(
            self::API_URL .
                $this->apiActions['post'] .
                '?cacheBust=' . $this->uniqid,
            [
                'method' => 'POST',
                'timeout' => 5,
                'body' => $this->getSiteData(),
                'headers' => 'CLIENT-SITE-ID: ' . md5(NONCE_KEY . NONCE_SALT . get_current_blog_id())
            ]
        );

        if (is_wp_error($response)) {
            return new \WP_Error($response->get_error_message());
        } else {
            if (wp_remote_retrieve_response_code($response) == 200) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the data about this installation
     *
     * @return array Array containing site data
     */
    private function getSiteData()
    {
        return [
            'uuid'      => md5(ABSPATH . get_home_url()),
            'website'   => get_home_url(),
            'name'      => get_bloginfo('name'),
            'dbVersion' => get_option('municipio_db_version'),
            'mods'      => $this->getSharedAttributes(),
            'css'       => wp_get_custom_css() ?? false,
        ];
    }

    /**
     * Get the attributes in theme mod to be shared
     *
     * @param   array $stack    Empty stack array
     * @return  array $stack    Populated stack array
     */
    private function getSharedAttributes($stack = [])
    {
        $mods = get_theme_mods();

        if (!empty($mods)) {
            foreach ($mods as $key => $mod) {
                //Prohibited keys
                if (in_array($key, ['load_design'])) {
                    continue;
                }

                if (array_key_exists($key, \Kirki::$all_fields)) {
                    $stack[$key] = $mod;
                }

                if (!empty($mod['font-family'])) {
                    $fontFileUrl = $this->getUploadedFontUrl($mod['font-family']);
                    if ($fontFileUrl) {
                        $stack['custom_fonts'][$mod['font-family']] = $fontFileUrl;
                    }
                }
            }
        }

        return $stack;
    }

    private function getUploadedFontUrl(string $fontFamily = ''): ?string
    {
        $uploadedFonts = array_diff_key(
            \Kirki\Module\Webfonts\Fonts::get_standard_fonts(),
            array_flip(["serif", "sans-serif", "monospace"])
        );
        if (!empty($uploadedFonts[$fontFamily])) {
            return \Municipio\Helper\File::getFileUrl($fontFamily);
        }

        return null;
    }
}
