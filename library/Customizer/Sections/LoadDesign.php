<?php

namespace Municipio\Customizer\Sections;

use Municipio\Customizer\KirkiField;
use Municipio\Customizer\Panel;
use Municipio\Customizer\PanelsRegistry;

class LoadDesign
{
    private const API_URL                 = 'https://customizer.municipio.tech/';
    private const LOAD_DESIGN_KEY         = 'load_design';
    private const EXCLUDE_LOAD_DESIGN_KEY = 'exclude_load_design';
    private $uniqueId                     = null;

    private $apiActions = [
        'post'   =>  "",
        'single' => 'id' . DIRECTORY_SEPARATOR
    ];

    public function __construct(string $sectionID)
    {
        KirkiField::addField([
            'type'      => 'select',
            'settings'  => self::LOAD_DESIGN_KEY,
            'label'     => esc_html__('Select a design', 'municipio'),
            'section'   => $sectionID,
            'default'   => false,
            'priority'  => 10,
            'choices'   => $this->loadOptions(),
            'transport' => 'postMessage'
        ]);

        KirkiField::addField(array(
            'settings'    => self::EXCLUDE_LOAD_DESIGN_KEY,
            'section'     => $sectionID,
            'type'        => 'select',
            'multiple'    => true,
            'label'       => esc_html__('Exclude from import', 'municipio'),
            'description' => esc_html__('Selected local settings will not be overriden on import.', 'municipio'),
            'choices'     => $this->getCustomizerSectionsAsOptions()
        ));

        // Disable info
        if (!$this->isBlogPublished()) {
            new \Kirki\Pro\Field\Divider(
                [
                    'settings' => 'load_design_state_divider',
                    'section'  => $sectionID,
                    'choices'  => [
                        'color' => '#ddd',
                    ],
                ]
            );

            new \Kirki\Pro\Field\Headline(
                [
                    'settings'    => 'load_design_state',
                    'label'       => esc_html__('Design Community is disabled', 'kirki-pro'),
                    'description' => esc_html__('This blog is currently not published. The design share is disabled until you site is published. Sites not accessible from the internet is always disabled.', 'kirki-pro'),
                    'section'     => $sectionID
                ]
            );
        }

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
     * Check if blog is published
     */
    private function isBlogPublished(): bool
    {
        if (!is_multisite()) {
            return true;
        }
        return get_blog_status(get_current_blog_id(), 'public') == 1;
    }

    private function getCustomizerSectionsAsOptions(): array
    {
        // Add core section due to unavalability through PanelsRegistry.
        $options = ['custom_css' => __('Additional CSS')];
        $panels  = PanelsRegistry::getInstance()->getRegisteredPanels();

        foreach ($panels as $panel) {
            $this->generateOptionsFromPanel($panel, $options);
        }

        return $options;
    }

    private function generateOptionsFromPanel(Panel $panel, array &$options)
    {

        if (!empty($sections = $panel->getSections())) {
            $optionGroupPanelPrefix = '';
            $optionGroupLabel       = empty($label = $panel->getTitle()) ? $panel->getID() : $label;

            if (!empty($parentPanelID = $panel->getPanel())) {
                $parentPanelTitle       = PanelsRegistry::getInstance()->getRegisteredPanels()[$parentPanelID]->getTitle();
                $optionGroupPanelPrefix = "{$parentPanelTitle} / ";
            }

            $options[$panel->getID()] = array("{$optionGroupPanelPrefix}{$optionGroupLabel}", []);

            foreach ($sections as $section) {
                $options[$panel->getID()][1][$section->getID()] = $section->getTitle();
            }
        }
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
                '?cacheBust=' . uniqid(),
            [
                'method'  => 'POST',
                'timeout' => 5,
                'body'    => $this->getSiteData(),
                'headers' => 'CLIENT-SITE-ID: ' . md5(NONCE_KEY . NONCE_SALT . get_current_blog_id())
            ]
        );

        if (is_wp_error($response)) {
            return new \WP_Error($response->get_error_message());
        } else {
            if (wp_remote_retrieve_response_code($response) == 200) {
                return $response;
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
                if (in_array($key, [self::LOAD_DESIGN_KEY])) {
                    continue;
                }

                if (in_array($key, [self::EXCLUDE_LOAD_DESIGN_KEY])) {
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
