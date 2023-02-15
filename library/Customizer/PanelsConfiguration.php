<?php

namespace Municipio\Customizer;

class PanelsConfiguration {

    public static function getPanelsConfiguration():array {
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
                        'initFields' => fn() => new \Municipio\Customizer\Sections\LoadDesign('municipio_customizer_panel_design_module'),
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
                ],
                'sections' => self::getArchivePanelSectionsConfiguaration('municipio_customizer_panel_archive')
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