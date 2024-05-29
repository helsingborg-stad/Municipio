<?php

namespace Municipio\Customizer;

use Municipio\Helper\ContentType as ContentTypeHelper;

class PanelsRegistry
{
    private static $instance             = null;
    private static bool $registerInvoked = false;
    private array $panels                = [];
    private array $sections              = [];
    public array $fields                 = [];

    private function __construct()
    {
        add_action('municipio_customizer_panel_registered', array($this, 'addPanelToRegistry'));
        add_action('municipio_customizer_section_registered', array($this, 'addSectionToRegistry'));
    }

    public function addPanelToRegistry(Panel $panel)
    {
        $this->panels[$panel->getID()] = $panel;
    }

    public function addSectionToRegistry(PanelSection $section)
    {
        $this->sections[$section->getID()] = $section;
    }

    /**
     * @return Panel[]
     */
    public function getRegisteredPanels(): array
    {
        return $this->panels;
    }

    /**
     * @return PanelSection[]
     */
    public function getRegisteredSections(): array
    {
        return $this->sections;
    }

    public function setFields(array $fields): void
    {
        $this->fields = array_merge($this->fields, $fields);
    }

    public function getRegisteredFields(): array
    {
        return $this->fields;
    }

    public static function getInstance(): PanelsRegistry
    {
        if (self::$instance === null) {
            self::$instance = new PanelsRegistry();
        }

        return self::$instance;
    }

    public function build(): void
    {

        if (self::$registerInvoked) {
            $method = __METHOD__;
            var_dump("{$method} can only be invoked once.");
            trigger_error("{$method} can only be invoked once.", E_USER_NOTICE);
            return;
        }

        self::$registerInvoked = true;
        self::registerArchivePanel();
        self::registerPostTypePanel();
        self::registerModulePanel();
        self::registerGeneralAppearancePanel();
        self::registerComponentAppearancePanel();
        self::registerNavMenusPanel();
        self::registerDesignLibraryPanel();
        self::registerContentTypesPanel();
    }

    public static function registerDesignLibraryPanel()
    {
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

    /**
     * Fetch public post types and exclude 'attachment'
     */
    public static function getPostTypes($args = [], $returnType = 'objects', $exclude = [])
    {
        $postTypes = get_post_types($args, $returnType);
        foreach ($exclude as $excludedType) {
            if (isset($postTypes[$excludedType])) {
                unset($postTypes[$excludedType]);
            }
        }
        return $postTypes;
    }

    /**
     * Register the content types panel
     */
    public static function registerContentTypesPanel()
    {
        $panelID   = 'municipio_customizer_panel_content_types';
        $postTypes = self::getPostTypes(
            ['public' => true],
            'objects',
            ['attachment']
        );

        $sections = array_map(function ($postType) use ($panelID) {
            $sectionId    = "{$panelID}_{$postType->name}";
            $contentTypes = ContentTypeHelper::getRegisteredContentTypes();

            if (empty($contentTypes)) {
                return false;
            }

            // Convert content types to choices for the dropdown and include a 'None' option
            $choices = ['' => esc_html__('None', 'municipio')] + array_reduce(array_keys($contentTypes), function ($carry, $key) use ($contentTypes) {
                $carry[$key] = $contentTypes[$key];
                return $carry;
            }, []);

            if (empty($choices)) {
                return false;
            }

            // Fetching a single post ID for the post type to use as a preview URL
            $posts      = get_posts(['post_type' => $postType->name, 'numberposts' => 1]);
            $previewUrl = !empty($posts) ? get_permalink($posts[0]->ID) : '';
            return KirkiPanelSection::create()
                ->setID($sectionId)
                ->setPanel($panelID)
                ->setTitle($postType->labels->name)
                ->setPreviewUrl($previewUrl)
                ->setFieldsCallback(function () use ($sectionId, $choices, $postType) {
                    // Dropdown for selecting content type
                    \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
                        'type'        => 'select',
                        'settings'    => $sectionId . '_content_type',
                        'label'       => esc_html__('Content Type', 'municipio'),
                        'description' => esc_html__('Select the content type for this post type.', 'municipio'),
                        'section'     => $sectionId,
                        'default'     => '',
                        'choices'     => $choices,
                    ]);

                    // Checkbox for opting out of content type page template
                    \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
                        'type'        => 'checkbox',
                        'settings'    => $sectionId . '_skip_content_type_template',
                        'label'       => esc_html__('Do not use template', 'municipio'),
                        'description' => sprintf(esc_html__('Do not use the custom template from the content type for this post type.', 'municipio')),
                        'section'     => $sectionId,
                        'default'     => '0',
                    ]);
                });
        }, $postTypes);

        KirkiPanel::create()
            ->setID($panelID)
            ->setTitle(esc_html__('Content Type Settings', 'municipio'))
            ->setDescription(esc_html__('Configure settings for different content types and their options.', 'municipio'))
            ->setPriority(1000) // Adjust the priority as needed
            ->addSections($sections)
            ->register();
    }

    public static function registerArchivePanel()
    {
        $panelID  = 'municipio_customizer_panel_archive';
        $archives = self::getArchives();
        $sections = array_map(function ($archive) use ($panelID) {
            $id = "{$panelID}_{$archive->name}";
            return KirkiPanelSection::create()
                ->setID($id)
                ->setPanel($panelID)
                ->setTitle($archive->label)
                ->setFieldsCallback(fn() => new \Municipio\Customizer\Sections\Archive($id, $archive))
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

    public static function registerPostTypePanel()
    {
        $panelID           = 'municipio_customizer_panel_post_type';
        $filteredPostTypes = self::getArchives();
        $sections          = array_map(function ($postType) use ($panelID) {
            $id = "{$panelID}_{$postType->name}";
            return KirkiPanelSection::create()
                ->setID($id)
                ->setPanel($panelID)
                ->setTitle($postType->label)
                ->setFieldsCallback(fn() => new \Municipio\Customizer\Sections\PostType($id, $postType));
        }, $filteredPostTypes);

        KirkiPanel::create()
            ->setID($panelID)
            ->setTitle(esc_html__('Post Types', 'municipio'))
            ->setDescription(esc_html__('Manage post types settings', 'municipio'))
            ->setPriority(2000)
            ->addSections($sections)
            ->register();
    }

    /* Module panel */
    public static function registerModulePanel()
    {
        KirkiPanel::create()
            ->setID('municipio_customizer_panel_design_module')
            ->setTitle(esc_html__('Module Apperance', 'municipio'))
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

    /* General panel */
    public static function registerGeneralAppearancePanel()
    {
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

    /* Component panel */
    public static function registerComponentAppearancePanel()
    {
        KirkiPanel::create()
            ->setID('municipio_customizer_panel_design_component')
            ->setTitle(esc_html__('Component Apperance', 'municipio'))
            ->setDescription(esc_html__('Manage design options on component level.', 'municipio'))
            ->setPriority(120)
            ->addSection(
                KirkiPanelSection::create()
                    ->setID('municipio_customizer_section_header')
                    ->setTitle(esc_html__('Header', 'municipio'))
                    ->setFieldsCallback(fn() => new \Municipio\Customizer\Sections\Header('municipio_customizer_section_header'))
            )
            ->addSection(
                KirkiPanelSection::create()
                    ->setID('municipio_customizer_section_siteselector')
                    ->setTitle(esc_html__('Siteselector', 'municipio'))
                    ->setFieldsCallback(fn() => new \Municipio\Customizer\Sections\Siteselector('municipio_customizer_section_siteselector'))
            )
            ->addSection(
                KirkiPanelSection::create()
                    ->setID('municipio_customizer_section_quicklinks')
                    ->setTitle(esc_html__('Quicklinks', 'municipio'))
                    ->setFieldsCallback(fn() => new \Municipio\Customizer\Sections\Quicklinks('municipio_customizer_section_quicklinks'))
            )
            ->addSection(
                KirkiPanelSection::create()
                    ->setID('municipio_customizer_section_component_button')
                    ->setTitle(esc_html__('Buttons', 'municipio'))
                    ->setFieldsCallback(fn() => new \Municipio\Customizer\Sections\Button('municipio_customizer_section_component_button'))
            )
            ->addSection(
                KirkiPanelSection::create()
                    ->setID('municipio_customizer_section_navigation')
                    ->setTitle(esc_html__('Navigations', 'municipio'))
                    ->setFieldsCallback(fn() => new \Municipio\Customizer\Sections\Navigation('municipio_customizer_section_navigation'))
            )
            ->addSection(
                KirkiPanelSection::create()
                    ->setID('municipio_customizer_section_mega_menu')
                    ->setTitle(esc_html__('Mega menu', 'municipio'))
                    ->setFieldsCallback(fn() => new \Municipio\Customizer\Sections\MegaMenu('municipio_customizer_section_mega_menu'))
            )

            ->addSection(
                KirkiPanelSection::create()
                    ->setID('municipio_customizer_section_component_openstreetmap')
                    ->setTitle(esc_html__('Open Street Map', 'municipio'))
                    ->setDescription(esc_html__('Settings for Open Street Maps.', 'municipio'))
                    ->setFieldsCallback(fn() => new \Municipio\Customizer\Sections\OpenStreetMap('municipio_customizer_section_component_openstreetmap'))
            )
            ->addSubPanel(
                KirkiPanel::create()
                    ->setID('municipio_customizer_section_component_slider')
                    ->setTitle(esc_html__('Slider', 'municipio'))
                    ->addSection(
                        KirkiPanelSection::create()
                        ->setID('municipio_customizer_section_default_component_slider')
                        ->setTitle(esc_html__('Regular Slider', 'municipio'))
                        ->setFieldsCallback(fn() => new \Municipio\Customizer\Sections\SliderDefault('municipio_customizer_section_default_component_slider'))
                    )
                    ->addSection(
                        KirkiPanelSection::create()
                        ->setID('municipio_customizer_section_hero_component_slider')
                        ->setTitle(esc_html__('Hero slider', 'municipio'))
                        ->setDescription(esc_html__('Settings for the slider in the hero area.', 'municipio'))
                        ->setFieldsCallback(fn() => new \Municipio\Customizer\Sections\SliderHero('municipio_customizer_section_hero_component_slider'))
                    )
            )
            ->addSubPanel(
                KirkiPanel::create()
                    ->setID('municipio_customizer_panel_component_footer')
                    ->setTitle(esc_html__('Footer', 'municipio'))
                    ->addSection(
                        KirkiPanelSection::create()
                            ->setID('municipio_customizer_section_component_footer_main')
                            ->setTitle(esc_html__('Main footer', 'municipio'))
                            ->setFieldsCallback(fn() => new \Municipio\Customizer\Sections\FooterMain('municipio_customizer_section_component_footer_main'))
                    )
                    ->addSection(
                        KirkiPanelSection::create()
                            ->setID('municipio_customizer_section_component_footer_subfooter')
                            ->setTitle(esc_html__('Sub footer', 'municipio'))
                            ->setFieldsCallback(fn() => new \Municipio\Customizer\Sections\FooterSub('municipio_customizer_section_component_footer_subfooter'))
                    )
            )
            ->addSection(
                KirkiPanelSection::create()
                    ->setID('municipio_customizer_section_divider')
                    ->setTitle(esc_html__('Divider', 'municipio'))
                    ->setFieldsCallback(fn() => new \Municipio\Customizer\Sections\Divider('municipio_customizer_section_divider'))
            )
            ->addSection(
                KirkiPanelSection::create()
                    ->setID('municipio_customizer_section_hero')
                    ->setTitle(esc_html__('Hero', 'municipio'))
                    ->setFieldsCallback(fn() => new \Municipio\Customizer\Sections\Hero('municipio_customizer_section_hero'))
            )
            ->addSection(
                KirkiPanelSection::create()
                    ->setID('municipio_customizer_section_field')
                    ->setTitle(esc_html__('Field', 'municipio'))
                    ->setFieldsCallback(fn() => new \Municipio\Customizer\Sections\Field('municipio_customizer_section_field'))
            )
            ->addSection(
                KirkiPanelSection::create()
                    ->setID('municipio_customizer_section_tags')
                    ->setTitle(esc_html__('Tags', 'municipio'))
                    ->setFieldsCallback(fn() => new \Municipio\Customizer\Sections\Tags('municipio_customizer_section_tags'))
            )->register();
    }

    /* Menu panel */
    public static function registerNavMenusPanel()
    {
        KirkiPanel::create()
            ->setID('nav_menus')
            ->addSection(
                KirkiPanelSection::create()
                    ->setID('municipio_customizer_section_menu')
                    ->setTitle(esc_html__('Menu behaviour', 'municipio'))
                    ->setDescription(esc_html__('Menu behaviour settings.', 'municipio'))
                    ->setFieldsCallback(fn() => new \Municipio\Customizer\Sections\Menu('municipio_customizer_section_menu'))
            )->register();
    }

    public static function getArchivePanelSectionsConfiguaration(string $parentPanelID): array
    {
        $archives        = self::getArchives();
        $archiveSections = [];

        if (is_array($archives) && !empty($archives)) {
            foreach ($archives as $archive) {
                $panelID           = $parentPanelID . "_" . $archive->name;
                $archiveSections[] =
                [
                    'id'         => $panelID,
                    'initFields' => fn() => new \Municipio\Customizer\Sections\Archive($panelID, $archive),
                    'args'       => [
                        'title'       => $archive->label,
                        'preview_url' => get_post_type_archive_link($archive->name)
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
            $args->taxonomies = self::getTaxonomies($postType);

            //Order By
            $args->orderBy = self::getOrderBy($postType);

            //Date source
            $args->dateSource = self::getDateSource($postType);

            //Add args to stack
            $postTypes[$postType] = $args;
        }

        $postTypes['author'] = (object) array(
            'name'              => 'author',
            'label'             => __('Author'),
            'has_archive'       => true,
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
        $stack      = [];
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
          'post_date'     => 'Date published',
          'post_modified' => 'Date modified',
          'post_title'    => 'Title',
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
            'post_date'     => 'Date published',
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
