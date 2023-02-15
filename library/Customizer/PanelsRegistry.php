<?php

namespace Municipio\Customizer;

class PanelsRegistry {

    public static $panels = [];

    public static function registerAllPanels() {
        self::registerDesignLibraryPanel();
        self::registerArchivePanel();
        self::registerModulePanel();
        self::registerGeneralAppearancePanel();
    }

    public static function registerDesignLibraryPanel() {
        KirkiPanel::create()
            ->setID('municipio_customizer_panel_designlib')
            ->setTitle(esc_html__('Design Library', 'municipio'))
            ->setDescription(esc_html__('Select a design made by other municipio users.', 'municipio'))
            ->setPriority(1000)
            ->addSection(
                KirkiPanelSection::create()
                    ->setID('municipio_customizer_panel_design_module')
                    ->setTitle(esc_html__('Load a design', 'municipio'))
                    ->setDescription(esc_html__('Want a new fresh design to your site? Use one of the options below to serve as a boilerplate!', 'municipio'))
                    ->setFieldsCallback(fn() => new \Municipio\Customizer\Sections\LoadDesign('municipio_customizer_panel_design_module'))
            )->register();
    }

    public static function registerArchivePanel() {
        $panelID = 'municipio_customizer_panel_archive';
        $archives = self::getArchives();
        $sections = array_map(function($archive) use ($panelID) {
            $id = "{$panelID}_{$archive->name}";
            return KirkiPanelSection::create()
                ->setID($id)
                ->setPanel($panelID)
                ->setTitle($archive->label)
                ->setFieldsCallback(fn() => new \Municipio\Customizer\Sections\Archive($id,$archive))
                ->setPreviewUrl(get_post_type_archive_link($archive->name));
        }, $archives);

        KirkiPanel::create()
            ->setID($panelID)
            ->setTitle(esc_html__('Archive Apperance', 'municipio'))
            ->setDescription(esc_html__('Manage apperance options on archives.', 'municipio'))
            ->setPriority(120)
            ->addSections($sections)
            ->register();
    }

    public static function registerModulePanel() {
        KirkiPanel::create()
            ->setID('municipio_customizer_panel_design_module')
            ->setTitle(esc_html__('Module Apperance', 'municipio'))
            ->setDescription(esc_html__('Module Apperance', 'municipio'))
            ->addSection(
                KirkiPanelSection::create()
                    ->setID('municipio_customizer_section_mod_contacts')
                    ->setTitle(esc_html__('Contacts', 'municipio'))
                    ->setActiveCallback(fn() => post_type_exists('mod-contacts'))
                    ->setFieldsCallback(fn() => new \Municipio\Customizer\Sections\Module\Contacts('municipio_customizer_section_mod_contacts'))
            )->addSection(
                KirkiPanelSection::create()
                    ->setID('municipio_customizer_section_mod_index')
                    ->setTitle(esc_html__('Index', 'municipio'))
                    ->setActiveCallback(fn() => post_type_exists('mod-contacts'))
                    ->setFieldsCallback(fn() => new \Municipio\Customizer\Sections\Module\Index('municipio_customizer_section_mod_index'))
            )->addSection(
                KirkiPanelSection::create()
                    ->setID('municipio_customizer_section_mod_inlay')
                    ->setTitle(esc_html__('Inlay list', 'municipio'))
                    ->setActiveCallback(fn() => post_type_exists('mod-inlaylist'))
                    ->setFieldsCallback(fn() => new \Municipio\Customizer\Sections\Module\Inlay('municipio_customizer_section_mod_inlay'))
            )->addSection(
                KirkiPanelSection::create()
                    ->setID('municipio_customizer_section_mod_localevent')
                    ->setTitle(esc_html__('Local Event', 'municipio'))
                    ->setActiveCallback(fn() => post_type_exists('mod-local-events'))
                    ->setFieldsCallback(fn() => new \Municipio\Customizer\Sections\Module\LocalEvent('municipio_customizer_section_mod_localevent'))
            )->addSection(
                KirkiPanelSection::create()
                    ->setID('municipio_customizer_section_mod_map')
                    ->setTitle(esc_html__('Maps', 'municipio'))
                    ->setActiveCallback(fn() => post_type_exists('mod-map'))
                    ->setFieldsCallback(fn() => new \Municipio\Customizer\Sections\Module\Map('municipio_customizer_section_mod_map'))
            )->addSection(
                KirkiPanelSection::create()
                    ->setID('municipio_customizer_section_mod_posts')
                    ->setTitle(esc_html__('Posts', 'municipio'))
                    ->setActiveCallback(fn() => post_type_exists('mod-posts'))
                    ->setFieldsCallback(fn() => new \Municipio\Customizer\Sections\Module\Posts('municipio_customizer_section_mod_posts'))
            )->addSection(
                KirkiPanelSection::create()
                    ->setID('municipio_customizer_section_mod_script')
                    ->setTitle(esc_html__('Script', 'municipio'))
                    ->setActiveCallback(fn() => post_type_exists('mod-script'))
                    ->setFieldsCallback(fn() => new \Municipio\Customizer\Sections\Module\Script('municipio_customizer_section_mod_script'))
            )->addSection(
                KirkiPanelSection::create()
                    ->setID('municipio_customizer_section_mod_sections_split')
                    ->setTitle(esc_html__('Sections Split', 'municipio'))
                    ->setActiveCallback(fn() => post_type_exists('mod-section-split'))
                    ->setFieldsCallback(fn() => new \Municipio\Customizer\Sections\Module\SectionsSplit('municipio_customizer_section_mod_sections_split'))
            )->addSection(
                KirkiPanelSection::create()
                    ->setID('municipio_customizer_section_mod_text')
                    ->setTitle(esc_html__('Text', 'municipio'))
                    ->setActiveCallback(fn() => post_type_exists('mod-text'))
                    ->setFieldsCallback(fn() => new \Municipio\Customizer\Sections\Module\Text('municipio_customizer_section_mod_text'))
            )->register();
    }
    
    public static function registerGeneralAppearancePanel() {
        KirkiPanel::create()
            ->setID('municipio_customizer_panel_design')
            ->setTitle(esc_html__('General Apperance', 'municipio'))
            ->setDescription(esc_html__('Manage site general design options.', 'municipio'))
            ->setPriority(120)
            ->addSection(
                KirkiPanelSection::create()
                    ->setID('municipio_customizer_section_logo')
                    ->setTitle(esc_html__('Logotypes', 'municipio'))
                    ->setFieldsCallback(fn() => new \Municipio\Customizer\Sections\Logo('municipio_customizer_section_logo'))
            )->addSection(
                KirkiPanelSection::create()
                    ->setID('municipio_customizer_section_general')
                    ->setTitle(esc_html__('General settings', 'municipio'))
                    ->setFieldsCallback(fn() => new \Municipio\Customizer\Sections\General('municipio_customizer_section_general'))
                    ->setPriority(120)
            )->addSection(
                KirkiPanelSection::create()
                    ->setID('municipio_customizer_section_colors')
                    ->setTitle(esc_html__('Colors', 'municipio'))
                    ->setFieldsCallback(fn() => new \Municipio\Customizer\Sections\Colors('municipio_customizer_section_colors'))
            )->addSection(
                KirkiPanelSection::create()
                    ->setID('municipio_customizer_section_typography')
                    ->setTitle(esc_html__('Typography', 'municipio'))
                    ->setDescription(esc_html__('Options for various Typography elements. This support BOF (Bring your own font). Simply upload your font in the media library, and it will be selectable.', 'municipio'))
                    ->setFieldsCallback(fn() => new \Municipio\Customizer\Sections\Typography('municipio_customizer_section_typography'))
            )->addSection(
                KirkiPanelSection::create()
                    ->setID('municipio_customizer_section_width')
                    ->setTitle(esc_html__('Page Widths', 'municipio'))
                    ->setDescription(esc_html__('Set the maximum page withs of different page types.', 'municipio'))
                    ->setFieldsCallback(fn() => new \Municipio\Customizer\Sections\Width('municipio_customizer_section_width'))
            )->addSection(
                KirkiPanelSection::create()
                    ->setID('municipio_customizer_section_border')
                    ->setTitle(esc_html__('Borders', 'municipio'))
                    ->setFieldsCallback(fn() => new \Municipio\Customizer\Sections\Borders('municipio_customizer_section_border'))
            )->addSection(
                KirkiPanelSection::create()
                    ->setID('municipio_customizer_section_radius')
                    ->setTitle(esc_html__('Rounded corners', 'municipio'))
                    ->setDescription(esc_html__('Adjust the roundness of corners on the site overall. The sizes are applied where they are suitable, additional component adjustments can be made under the components tab.', 'municipio'))
                    ->setFieldsCallback(fn() => new \Municipio\Customizer\Sections\Radius('municipio_customizer_section_radius'))
            )->addSection(
                KirkiPanelSection::create()
                    ->setID('municipio_customizer_section_padding')
                    ->setTitle(esc_html__('Padding', 'municipio'))
                    ->setFieldsCallback(fn() => new \Municipio\Customizer\Sections\Padding('municipio_customizer_section_padding'))
            )->addSection(
                KirkiPanelSection::create()
                    ->setID('municipio_customizer_section_shadow')
                    ->setTitle(esc_html__('Drop Shadows', 'municipio'))
                    ->setFieldsCallback(fn() => new \Municipio\Customizer\Sections\Shadow('municipio_customizer_section_shadow'))
            )->addSection(
                KirkiPanelSection::create()
                    ->setID('municipio_customizer_section_search')
                    ->setTitle(esc_html__('Search', 'municipio'))
                    ->setFieldsCallback(fn() => new \Municipio\Customizer\Sections\Search('municipio_customizer_section_search'))
            )->register();
    }

    public static function getPanelsConfiguration():array {
        return
        [
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
                    [
                        'id' => 'municipio_customizer_panel_component_footer',
                        'args' => [
                            'title'       => esc_html__('Footer', 'municipio'),
                            'description' => esc_html__('Footer settings.', 'municipio'),
                        ],
                        'sections' => [
                            [
                                'id' => 'municipio_customizer_section_component_footer_main',
                                'initFields' => fn() => new \Municipio\Customizer\Sections\FooterMain('municipio_customizer_section_component_footer_main'),
                                'args' => [
                                    'title'       => esc_html__('Main footer', 'municipio'),
                                    'description' => esc_html__('Main footer settings.', 'municipio'),
                                ]
                            ],
                            [
                                'id' => 'municipio_customizer_section_component_footer_subfooter',
                                'initFields' => fn() => new \Municipio\Customizer\Sections\FooterSub('municipio_customizer_section_component_footer_subfooter'),
                                'args' => [
                                    'title'       => esc_html__('Sub footer', 'municipio'),
                                    'description' => esc_html__('Sub footer settings.', 'municipio'),
                                ]
                            ],
                        ]
                    ],
                    [
                        'id' => 'municipio_customizer_section_divider',
                        'initFields' => fn() => new \Municipio\Customizer\Sections\Divider('municipio_customizer_section_divider'),
                        'args' => [
                            'title'       => esc_html__('Divider', 'municipio'),
                            'description' => esc_html__('Divider settings.', 'municipio'),
                        ]
                    ],
                    [
                        'id' => 'municipio_customizer_section_hero',
                        'initFields' => fn() => new \Municipio\Customizer\Sections\Hero('municipio_customizer_section_hero'),
                        'args' => [
                            'title'       => esc_html__('Hero', 'municipio'),
                            'description' => esc_html__('Hero settings.', 'municipio'),
                        ]
                    ],
                    [
                        'id' => 'municipio_customizer_section_field',
                        'initFields' => fn() => new \Municipio\Customizer\Sections\Field('municipio_customizer_section_field'),
                        'args' => [
                            'title'       => esc_html__('Field', 'municipio'),
                            'description' => esc_html__('Field settings.', 'municipio'),
                        ]
                    ],
                ]
            ],
            [
                'id' => 'nav_menus',
                'sections' => [
                    [
                        'id' => 'municipio_customizer_section_menu',
                        'initFields' => fn() => new \Municipio\Customizer\Sections\Menu('municipio_customizer_section_menu'),
                        'args' => [
                            'title'       => esc_html__('Menu behaviour', 'municipio'),
                            'description' => esc_html__('Menu behaviour settings.', 'municipio'),
                        ]
                    ],
                ]
            ]
        ];
    }

    public static function getArchivePanelSectionsConfiguaration(string $parentPanelID):array {
        $archives = self::getArchives();
        $archiveSections = [];
        
        if (is_array($archives) && !empty($archives)) {
            foreach ($archives as $archive) {
                $panelID = $parentPanelID . "_" . $archive->name;
                $archiveSections[] =
                [
                    'id' => $panelID,
                    'initFields' => fn() => new \Municipio\Customizer\Sections\Archive($panelID,$archive),
                    'args' => [
                        'title' => $archive->label,
                        'preview_url'   => get_post_type_archive_link($archive->name)
                    ]
                ];
            }
        }

        return $archiveSections;
    }

    /**
     * Fetch archives
     *
     * @return array
     */
    private static function getArchives(): array
    {
        $postTypes = array();

        foreach ((array) get_post_types() as $key => $postType) {
            $args = get_post_type_object($postType);

            if (!$args->public || in_array($args->name, ['page', 'attachment'])) {
                continue;
            }

            //Taxonomies
            $args->taxonomies   = self::getTaxonomies($postType);

            //Order By
            $args->orderBy      = self::getOrderBy($postType);

            //Date source
            $args->dateSource   = self::getDateSource($postType);

            //Add args to stack
            $postTypes[$postType] = $args;
        }

        $postTypes['author'] = (object) array(
            'name' => 'author',
            'label' => __('Author'),
            'has_archive' => true,
            'is_author_archive' => true
        );

        return $postTypes;
    }

    /**
     * Get taxonomies for post type
     *
     * @param string $postType
     * @return array
     */
    private static function getTaxonomies($postType): array
    {
        $stack = [];
        $taxonomies = get_object_taxonomies($postType, 'objects');

        if (is_array($taxonomies) && !empty($taxonomies)) {
            foreach ($taxonomies as $taxonomy) {
                if ($taxonomy->public) {
                    $stack[$taxonomy->name] = $taxonomy->label;
                }
            }

            return $stack;
        }

        return [];
    }

    /**
     * Get order by options for post type
     *
     * @param string $postType
     * @return array
     */
    private static function getOrderBy($postType): array
    {
        $metaKeys = array(
          'post_date'  => 'Date published',
          'post_modified' => 'Date modified',
          'post_title' => 'Title',
        );

        $metaKeysRaw = \Municipio\Helper\Post::getPosttypeMetaKeys($postType);

        if (isset($metaKeysRaw) && is_array($metaKeysRaw) && !empty($metaKeysRaw)) {
            foreach ($metaKeysRaw as $metaKey) {
                $metaKeys[$metaKey] = $metaKey;
            }
        }

        return $metaKeys;
    }

    /**
     * Get list of date sources
     *
     * @param string $postType
     * @return array
     */
    private static function getDateSource($postType): array
    {
        $metaKeys = array(
            'post_date'  => 'Date published',
            'post_modified' => 'Date modified',
        );

        $metaKeysRaw = \Municipio\Helper\Post::getPosttypeMetaKeys($postType);

        if (isset($metaKeysRaw) && is_array($metaKeysRaw) && !empty($metaKeysRaw)) {
            foreach ($metaKeysRaw as $metaKey) {
                $metaKeys[$metaKey] = $metaKey;
            }
        }

        return $metaKeys;
    }
}