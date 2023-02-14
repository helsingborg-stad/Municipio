<?php

namespace Municipio;

use Kirki\Compatibility\Kirki;
use Municipio\Customizer\KirkiPanel;
use Municipio\Customizer\KirkiPanelSection;
use Municipio\Customizer\Panel;
use Municipio\Customizer\PanelArgs;

class Customizer
{
    public const KIRKI_CONFIG = "municipio_config";

    public static $panels = array();

    public function __construct()
    {
        //Load embedded kirki
        $this->loadEmbeddedKirki();

        //Kirki failed to load, handle
        add_action('init', function () {
            if (class_exists("Kirki")) {
                return $this->init();
            }

            wp_die(
                __("Kirki Customizer framework is required"),
                __("Plugin install required"),
                [
                    'link_url' => "https://github.com/kirki-framework/kirki.git",
                    'link_text' => __("Install plugin", 'municipio')
                ]
            );
        }, 10);

        /**
         * Fixes issue when using :root selector in
         * output args for Gutenberg editor.
         *
         * Issue adressed here: https://github.com/kirki-framework/kirki/issues/2461.
         * When resolved, this can be removed.
         */
        add_filter('kirki_municipio_config_dynamic_css', function ($styles) {
            $isEditor = (isset($_GET['editor']) && $_GET['editor'] == '1');
            $isStyles = (isset($_GET['action']) && $_GET['action'] == 'kirki-styles');

            if ($isEditor && $isStyles) {
                return str_replace(
                    '.editor-styles-wrapper :root',
                    '.editor-styles-wrapper',
                    $styles
                );
            }
            return $styles;
        }, 20);

        /**
         * Loads functionality to load a certain page
         * for each expanded panel.
         */
        add_action(
            'customize_controls_enqueue_scripts',
            array($this, 'addPreviewPageSwitches')
        );

        /**
         * Collects all panels view a preview url.
         */
        add_action('kirki_section_added', function ($id, $args) {
            if (isset($args['preview_url']) && filter_var($args['preview_url'], FILTER_VALIDATE_URL)) {
                self::$panels[$id] = $args['preview_url'];
            }
        }, 10, 2);
    }

    /**
     * Load embedded kirki
     *
     * @return void
     */
    public function loadEmbeddedKirki()
    {
        $kirkiFilePath = rtrim(MUNICIPIO_PATH, '/') . '/vendor/kirki/kirki.php';
        if (file_exists($kirkiFilePath)) {
            include_once($kirkiFilePath);
        }
    }

    /**
     * Initialize the Customizer
     *
     * @return void
     */
    public function addPreviewPageSwitches()
    {
        wp_register_script(
            'municipio-customizer-preview',
            get_template_directory_uri() . '/assets/dist/' . \Municipio\Helper\CacheBust::name('js/customizer-preview.js'),
            array( 'jquery', 'customize-controls' ),
            false,
            true
        );
        wp_localize_script(
            'municipio-customizer-preview',
            'customizerPanelPreviewUrls',
            (array) self::$panels
        );
        wp_enqueue_script('municipio-customizer-preview');
    }

    /**
     * Init customizer toolset
     *
     * @return void
     */
    public function init()
    {
        if (!defined("WEB_FONT_DISABLE_INLINE")) {
            define("WEB_FONT_DISABLE_INLINE", true);
        }

        Kirki::add_config(self::KIRKI_CONFIG, array(
            'capability'        => 'edit_theme_options',
            'option_type'       => 'theme_mod',
            'gutenberg_support' => false
        ));

        // Custom fonts support (parse uploaded fonts)
        new \Kirki\Module\FontUploads();

        //Applicators [Stuff that make effect on the frontend]
        new \Municipio\Customizer\Applicators\Modifiers();
        new \Municipio\Customizer\Applicators\ComponentData();
        new \Municipio\Customizer\Applicators\ControllerVariables();
        new \Municipio\Customizer\Applicators\Css();

        $this->registerPanels(KirkiPanel::class, KirkiPanelSection::class);
        
        new \Municipio\Customizer\Panels\Component('municipio_customizer_panel_design_component');
        new \Municipio\Customizer\Panels\Archive('municipio_customizer_panel_archive');
        new \Municipio\Customizer\Sections\Menu('nav_menus');
        
        new \Municipio\Customizer\Sections\LoadDesign('municipio_customizer_section_designlib');

        //Define Typography Customizer
        new \Municipio\Customizer\Controls\Typography();
    }

    private function registerPanels() {

        $panelsConfig = $this->getPanelsConfiguration();
        
        foreach ($panelsConfig as $config) {
            $this->registerPanel($config);
        }
    }

    private function registerPanel(array $panelConfig) {

        new KirkiPanel($panelConfig['id'], $panelConfig['args'] ?? []);

        if( isset($panelConfig['sections']) ) {
            foreach ($panelConfig['sections'] as $sectionConfig) {
                $sectionConfig['args']['panel'] = $panelConfig['id'];
                $this->registerSection( $sectionConfig );
            }
        }
    }

    private function registerSection(array $sectionConfig) {

        if( isset($sectionConfig['sections']) ) {
            $this->registerPanel($sectionConfig);
            return;
        }

        new KirkiPanelSection($sectionConfig['id'], $sectionConfig['args'] );

        if( isset($sectionConfig['initFields'])) {
            $sectionConfig['initFields']();
        }
    }

    private function getPanelsConfiguration():array {
        return
        [
            [
                'id' => 'municipio_customizer_panel_designlib',
                'args' => [
                    'title'        =>  esc_html__('Design Library', 'municipio'),
                    'description'  =>  esc_html__('Select a design made by other municipio users.', 'municipio'),
                    'priority'     =>  1000,
                ],
                'sections' => [
                    [
                        'id' => 'municipio_customizer_panel_design_module',
                        'args' =>
                        [
                            'title'          => esc_html__('Load a design', 'municipio'),
                            'description'    => esc_html__('Want a new fresh design to your site? Use one of the options below to serve as a boilerplate!', 'municipio'),
                        ]
                    ]
                ]
            ],
            [
                'id' => 'municipio_customizer_panel_archive',
                'args' =>
                [
                    'title' =>  esc_html__('Archive Apperance', 'municipio'),
                    'description' =>  esc_html__('Manage apperance options on archives.', 'municipio'),
                    'priority' =>  120,
                ]
            ],
            [
                'id' => 'municipio_customizer_panel_design_module',
                'args' =>
                [
                    'title' =>  esc_html__('Module Apperance', 'municipio'),
                    'description' =>  esc_html__('Module Apperance', 'municipio'),
                    'priority' =>  120,
                ],
                'sections' =>
                [
                    [
                        'id' => 'municipio_customizer_section_mod_contacts',
                        'initFields' => fn() => new \Municipio\Customizer\Sections\Module\Contacts('municipio_customizer_section_mod_contacts'),
                        'args' => [
                            'title'       => esc_html__('Contacts', 'municipio'),
                            'active_callback' => fn() => post_type_exists('mod-contacts')
                        ]
                    ],
                    [
                        'id' => 'municipio_customizer_section_mod_index',
                        'initFields' => fn() => new \Municipio\Customizer\Sections\Module\Index('municipio_customizer_section_mod_index'),
                        'args' => [
                            'title'       => esc_html__('Index', 'municipio'),
                            'active_callback' => fn() => post_type_exists('mod-contacts')
                        ]
                    ],
                    [
                        'id' => 'municipio_customizer_section_mod_inlay',
                        'initFields' => fn() => new \Municipio\Customizer\Sections\Module\Inlay('municipio_customizer_section_mod_inlay'),
                        'args' => [
                            'title'       => esc_html__('Inlay list', 'municipio'),
                            'active_callback' => fn() => post_type_exists('mod-inlaylist')
                        ]
                    ],
                    [
                        'id' => 'municipio_customizer_section_mod_localevent',
                        'initFields' => fn() => new \Municipio\Customizer\Sections\Module\LocalEvent('municipio_customizer_section_mod_localevent'),
                        'args' => [
                            'title'       => esc_html__('Local Event', 'municipio'),
                            'active_callback' => fn() => post_type_exists('mod-local-events')
                        ]
                    ],
                    [
                        'id' => 'municipio_customizer_section_mod_map',
                        'initFields' => fn() => new \Municipio\Customizer\Sections\Module\Map('municipio_customizer_section_mod_map'),
                        'args' => [
                            'title'       => esc_html__('Maps', 'municipio'),
                            'active_callback' => fn() => post_type_exists('mod-map')
                        ]
                    ],
                    [
                        'id' => 'municipio_customizer_section_mod_posts',
                        'initFields' => fn() => new \Municipio\Customizer\Sections\Module\Posts('municipio_customizer_section_mod_posts'),
                        'args' => [
                            'title'       => esc_html__('Posts', 'municipio'),
                            'active_callback' => fn() => post_type_exists('mod-posts')
                        ]
                    ],
                    [
                        'id' => 'municipio_customizer_section_mod_script',
                        'initFields' => fn() => new \Municipio\Customizer\Sections\Module\Script('municipio_customizer_section_mod_script'),
                        'args' => [
                            'title'       => esc_html__('Script', 'municipio'),
                            'active_callback' => fn() => post_type_exists('mod-script')
                        ]
                    ],
                    [
                        'id' => 'municipio_customizer_section_mod_sections_split',
                        'initFields' => fn() => new \Municipio\Customizer\Sections\Module\SectionsSplit('municipio_customizer_section_mod_sections_split'),
                        'args' => [
                            'title'       => esc_html__('Sections Split', 'municipio'),
                            'active_callback' => fn() => post_type_exists('mod-section-split')
                        ]
                    ],
                    [
                        'id' => 'municipio_customizer_section_mod_text',
                        'initFields' => fn() => new \Municipio\Customizer\Sections\Module\Text('municipio_customizer_section_mod_text'),
                        'args' => [
                            'title'       => esc_html__('Text', 'municipio'),
                            'active_callback' => fn() => post_type_exists('mod-text')
                        ]
                    ],
                    [
                        'id' => 'municipio_customizer_section_mod_video',
                        'initFields' => fn() => new \Municipio\Customizer\Sections\Module\Video('municipio_customizer_section_mod_video'),
                        'args' => [
                            'title'       => esc_html__('Video', 'municipio'),
                            'active_callback' => fn() => post_type_exists('mod-video')
                        ]
                    ],
                ]
            ],
            [
                'id' => 'municipio_customizer_panel_design',
                'args' =>
                [
                    'title'       => esc_html__('General Apperance', 'municipio'),
                    'description' => esc_html__('Manage site general design options.', 'municipio'),
                    'priority'    => 120,
                ],
                'sections' =>
                [
                    [
                        'id' => 'municipio_customizer_section_logo',
                        'initFields' => fn() => new \Municipio\Customizer\Sections\Logo('municipio_customizer_section_logo'),
                        'args' => [
                            'title'         => esc_html__('Logo', 'municipio'),
                            'description'   => esc_html__('Logo settings.', 'municipio'),
                        ]
                    ],
                    [
                        'id' => 'municipio_customizer_section_general',
                        'initFields' => fn() => new \Municipio\Customizer\Sections\General('municipio_customizer_section_general'),
                        'args' => [
                            'title'       => esc_html__('General', 'municipio'),
                            'description' => esc_html__('General settings.', 'municipio'),
                        ]
                    ],
                    [
                        'id' => 'municipio_customizer_section_colors',
                        'initFields' => fn() => new \Municipio\Customizer\Sections\Colors('municipio_customizer_section_colors'),
                        'args' => [
                            'title'       => esc_html__('Colors', 'municipio'),
                            'description' => esc_html__('Colors used across the theme.', 'municipio'),
                        ]
                    ],
                    [
                        'id' => 'municipio_customizer_section_typography',
                        'initFields' => fn() => new \Municipio\Customizer\Sections\Typography('municipio_customizer_section_typography'),
                        'args' => [
                            'title'       => esc_html__('Typography', 'municipio'),
                            'description' => esc_html__('Options for various Typography elements. This support BOF (Bring your own font). Simply upload your font in the media library, and it will be selectable.', 'municipio'),
                        ]
                    ],
                    [
                        'id' => 'municipio_customizer_section_width',
                        'initFields' => fn() => new \Municipio\Customizer\Sections\Width('municipio_customizer_section_width'),
                        'args' => [
                            'title'       => esc_html__('Page Widths', 'municipio'),
                            'description' => esc_html__('Set the maximum page withs of different page types.', 'municipio'),
                        ]
                    ],
                    [
                        'id' => 'municipio_customizer_section_border',
                        'initFields' => fn() => new \Municipio\Customizer\Sections\Borders('municipio_customizer_section_border'),
                        'args' => [
                            'title'       => esc_html__('Borders', 'municipio'),
                            'description' => esc_html__('Adjust general borders', 'municipio'),
                        ]
                    ],
                    [
                        'id' => 'municipio_customizer_section_radius',
                        'initFields' => fn() => new \Municipio\Customizer\Sections\Radius('municipio_customizer_section_radius'),
                        'args' => [
                            'title'       => esc_html__('Rounded corners', 'municipio'),
                            'description' => esc_html__('Adjust the roundness of corners on the site overall. The sizes are applied where they are suitable, additional component adjustments can be made under the components tab.', 'municipio'),
                        ]
                    ],
                    [
                        'id' => 'municipio_customizer_section_padding',
                        'initFields' => fn() => new \Municipio\Customizer\Sections\Padding('municipio_customizer_section_padding'),
                        'args' => [
                            'title'       => esc_html__('Padding', 'municipio'),
                            'description' => esc_html__('Padding settings.', 'municipio'),
                        ]
                    ],
                    [
                        'id' => 'municipio_customizer_section_shadow',
                        'initFields' => fn() => new \Municipio\Customizer\Sections\Shadow('municipio_customizer_section_shadow'),
                        'args' => [
                            'title'       => esc_html__('Drop Shadows', 'municipio'),
                            'description' => esc_html__('Adjust general drop shadows. ', 'municipio'),
                        ]
                    ],
                    [
                        'id' => 'municipio_customizer_section_search',
                        'initFields' => fn() => new \Municipio\Customizer\Sections\Search('municipio_customizer_section_search'),
                        'args' => [
                            'title'         => esc_html__('Search', 'municipio'),
                            'description' => esc_html__('Adjust general drop shadows. ', 'municipio'),
                        ]
                    ]
                ]
            ],
            [
                'id' => 'municipio_customizer_panel_design_component',
                'args' => [
                    'title'       => esc_html__('Component Apperance', 'municipio'),
                    'description' => esc_html__('Manage design options on component level.', 'municipio'),
                    'priority'    => 120,
                ],
                'sections' => [
                    [
                        'id' => 'municipio_customizer_section_header',
                        'initFields' => fn() => new \Municipio\Customizer\Sections\Header('municipio_customizer_section_header'),
                        'args' => [
                            'title'       => esc_html__('Header', 'municipio'),
                            'description' => esc_html__('Header settings.', 'municipio'),
                        ]
                    ],
                    [
                        'id' => 'municipio_customizer_section_quicklinks',
                        'initFields' => fn() => new \Municipio\Customizer\Sections\Quicklinks('municipio_customizer_section_quicklinks'),
                        'args' => [
                            'title'       => esc_html__('Quicklinks', 'municipio'),
                            'description' => esc_html__('Quicklinks settings.', 'municipio'),
                        ]
                    ],
                    [
                        'id' => 'municipio_customizer_section_component_button',
                        'initFields' => fn() => new \Municipio\Customizer\Sections\Button('municipio_customizer_section_component_button'),
                        'args' => [
                            'title'       => esc_html__('Buttons', 'municipio'),
                            'description' => esc_html__('Settings for buttons.', 'municipio'),
                        ]
                    ],
                    [
                        'id' => 'municipio_customizer_section_hamburger_menu',
                        'initFields' => fn() => new \Municipio\Customizer\Sections\HamburgerMenu('municipio_customizer_section_hamburger_menu'),
                        'args' => [
                            'title'       => esc_html__('Hamburger menu', 'municipio'),
                            'description' => esc_html__('Hamburger menu settings.', 'municipio'),
                        ]
                    ],
                    [
                        'id' => 'municipio_customizer_section_component_slider',
                        'args' => [
                            'title'       => esc_html__('Slider', 'municipio'),
                            'description' => esc_html__('Settings for sliders.', 'municipio'),
                        ],
                        'sections' => [
                            [
                                'id' => 'municipio_customizer_section_default_component_slider',
                                'initFields' => fn() => new \Municipio\Customizer\Sections\SliderDefault('municipio_customizer_section_default_component_slider'),
                                'args' => [
                                    'title'       => esc_html__('Regular Slider', 'municipio'),
                                    'description' => esc_html__('Settings for sliders.', 'municipio'),
                                ]
                            ],
                            [
                                'id' => 'municipio_customizer_section_hero_component_slider',
                                'initFields' => fn() => new \Municipio\Customizer\Sections\SliderHero('municipio_customizer_section_hero_component_slider'),
                                'args' => [
                                    'title'       => esc_html__('Hero slider', 'municipio'),
                                    'description' => esc_html__('Settings for the slider in the hero area.', 'municipio'),
                                ]
                            ]
                        ]
                    ],
                ]
            ],
            [
                'id' => 'nav_menus',
            ]
        ];
    }
}