<?php

namespace Municipio\Customizer;

class PanelsRegistry
{
    private static $instance = null;
    private static bool $registerInvoked = false;
    private array $panels = [];
    private array $sections = [];
    public array $fields = [];

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

    public function addRegisteredField(array $field): void
    {
        $this->fields[] = $field;
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
            trigger_error("{$method} can only be invoked once.", E_USER_NOTICE);
            return;
        }

        self::$registerInvoked = true;
        self::registerSiteIdentityFields();
        self::registerAppearancePanel();
        self::registerHeaderPanel();
        self::registerNavigationPanel();
        self::registerHeroSection();
        self::registerFooterSection();
        self::registerCardsSection();
        self::registerSliderPanel();
        self::registerDividerSection();
        self::registerTagsSection();
        self::registerOpenStreetMapSection();
        self::registerArchivePanel();
        self::registerErrorPagesPanel();
        self::registerDesignLibraryPanel();
    }

    public static function registerSiteIdentityFields(): void
    {
        new \Municipio\Customizer\Sections\Logo('title_tagline');
    }

    public static function registerDesignLibraryPanel()
    {
        $panelId = 'municipio_customizer_panel_post_types';

        $filteredPostTypes = self::getArchives(['attachment']);
        $sections = array_map(function ($postType) use ($panelId) {
            $id = "{$panelId}_{$postType->name}";
            return CustomizerPanelSection::create()
                ->setID($id)
                ->setPanel($panelId)
                ->setTitle($postType->label)
                ->setFieldsCallback(fn() => new \Municipio\Customizer\Sections\PostType($id, $postType));
        }, $filteredPostTypes);

        // CustomizerPanel::create()
        //     ->setID('municipio_customizer_panel_designlib')
        //     ->setTitle(esc_html__('Design Library', 'municipio'))
        //     ->setDescription(esc_html__('Select a design made by other municipio users.', 'municipio'))
        //     ->setPriority(1000)
        //     ->addSection(
        //         CustomizerPanelSection::create()
        //             ->setID('municipio_customizer_panel_design_module')
        //             ->setTitle(esc_html__('Load a design', 'municipio'))
        //             ->setDescription(esc_html__('Want a new fresh design to your site? Use one of the options below to serve as a boilerplate!', 'municipio'))
        //             ->setFieldsCallback(fn() => new \Municipio\Customizer\Sections\LoadDesign('municipio_customizer_panel_design_module')),
        //     )
        //     ->addSubPanel(
        //         CustomizerPanel::create()->setID($panelId)->setTitle(esc_html__('Load design for individual post types', 'municipio'))->setDescription(esc_html__('Manage post types settings', 'municipio'))->addSections($sections),
        //     )
        //     ->register();
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

    /* Appearance panel */
    public static function registerAppearancePanel()
    {
        CustomizerPanel::create()
            ->setID('municipio_customizer_panel_design')
            ->setTitle(esc_html__('Appearance', 'municipio'))
            ->setDescription(esc_html__('Manage site-wide design options.', 'municipio'))
            ->setPriority(10)
            ->addSection(
                CustomizerPanelSection::create()
                    ->setID('municipio_customizer_section_general')
                    ->setTitle(esc_html__('General', 'municipio'))
                    ->setFieldsCallback(fn() => new \Municipio\Customizer\Sections\General('municipio_customizer_section_general')),
            )
            ->addSection(
                CustomizerPanelSection::create()
                    ->setID('municipio_customizer_section_width')
                    ->setTitle(esc_html__('Page Widths', 'municipio'))
                    ->setFieldsCallback(fn() => new \Municipio\Customizer\Sections\Width('municipio_customizer_section_width')),
            )
            ->addSection(
                CustomizerPanelSection::create()
                    ->setID('municipio_customizer_section_icons')
                    ->setTitle(esc_html__('Icons', 'municipio'))
                    ->setFieldsCallback(fn() => new \Municipio\Customizer\Sections\Icons('municipio_customizer_section_icons')),
            )
            ->addSection(
                CustomizerPanelSection::create()
                    ->setID('municipio_customizer_section_search')
                    ->setTitle(esc_html__('Search', 'municipio'))
                    ->setFieldsCallback(fn() => new \Municipio\Customizer\Sections\Search('municipio_customizer_section_search')),
            )
            ->register();
    }

    /* Header panel */
    public static function registerHeaderPanel()
    {
        CustomizerPanel::create()
            ->setID('municipio_customizer_header_panel')
            ->setTitle(esc_html__('Header', 'municipio'))
            ->setDescription(esc_html__('Manage header layout and appearance.', 'municipio'))
            ->setPriority(20)
            ->addSection(
                CustomizerPanelSection::create()
                    ->setID('municipio_customizer_section_header_panel_layout')
                    ->setTitle(esc_html__('Layout', 'municipio'))
                    ->setFieldsCallback(fn() => new \Municipio\Customizer\Sections\Header\Layout('municipio_customizer_section_header_panel_layout'))
                    ->setTabs([
                        'general' => [
                            'label' => esc_html__('General', 'municipio'),
                        ],
                        'flexible' => [
                            'label' => esc_html__('Flexible', 'municipio'),
                        ],
                    ]),
            )
            ->addSection(
                CustomizerPanelSection::create()
                    ->setID('municipio_customizer_section_header_panel_appearance')
                    ->setTitle(esc_html__('Appearance', 'municipio'))
                    ->setFieldsCallback(fn() => new \Municipio\Customizer\Sections\Header\Appearance('municipio_customizer_section_header_panel_appearance')),
            )
            ->addSection(
                CustomizerPanelSection::create()
                    ->setID('municipio_customizer_section_header_panel_logotype')
                    ->setTitle(esc_html__('Logotype', 'municipio'))
                    ->setFieldsCallback(fn() => new \Municipio\Customizer\Sections\Header\Logotype('municipio_customizer_section_header_panel_logotype')),
            )
            ->addSection(
                CustomizerPanelSection::create()
                    ->setID('municipio_customizer_section_header_panel_login_logout')
                    ->setTitle(esc_html__('Login / Logout', 'municipio'))
                    ->setFieldsCallback(fn() => new \Municipio\Customizer\Sections\Header\LoginLogout('municipio_customizer_section_header_panel_login_logout')),
            )
            ->register();
    }

    /* Navigation panel — adds sections to the WordPress native nav_menus panel */
    public static function registerNavigationPanel()
    {
        CustomizerPanel::create()
            ->setID('nav_menus')
            ->addSection(
                CustomizerPanelSection::create()
                    ->setID('municipio_customizer_section_menu')
                    ->setTitle(esc_html__('Behaviour', 'municipio'))
                    ->setDescription(esc_html__('Menu behaviour settings.', 'municipio'))
                    ->setFieldsCallback(fn() => new \Municipio\Customizer\Sections\Menu\Behaviour('municipio_customizer_section_menu')),
            )
            ->addSection(
                CustomizerPanelSection::create()
                    ->setID('municipio_customizer_section_breadcrumbs')
                    ->setTitle(esc_html__('Breadcrumbs', 'municipio'))
                    ->setFieldsCallback(fn() => new \Municipio\Customizer\Sections\Menu\Breadcrumbs('municipio_customizer_section_breadcrumbs')),
            )
            ->addSection(
                CustomizerPanelSection::create()
                    ->setID('municipio_customizer_section_drawer')
                    ->setTitle(esc_html__('Drawer', 'municipio'))
                    ->setFieldsCallback(fn() => new \Municipio\Customizer\Sections\Menu\Drawer('municipio_customizer_section_drawer')),
            )
            ->addSection(
                CustomizerPanelSection::create()
                    ->setID('municipio_customizer_section_mega_menu')
                    ->setTitle(esc_html__('Mega menu', 'municipio'))
                    ->setFieldsCallback(fn() => new \Municipio\Customizer\Sections\Menu\MegaMenu('municipio_customizer_section_mega_menu')),
            )
            ->addSection(
                CustomizerPanelSection::create()
                    ->setID('municipio_customizer_section_quicklinks')
                    ->setTitle(esc_html__('Quicklinks', 'municipio'))
                    ->setFieldsCallback(fn() => new \Municipio\Customizer\Sections\Menu\Quicklinks('municipio_customizer_section_quicklinks')),
            )
            ->addSection(
                CustomizerPanelSection::create()
                    ->setID('municipio_customizer_section_siteselector')
                    ->setTitle(esc_html__('Site selector', 'municipio'))
                    ->setFieldsCallback(fn() => new \Municipio\Customizer\Sections\Menu\Siteselector('municipio_customizer_section_siteselector')),
            )
            ->addSection(
                CustomizerPanelSection::create()
                    ->setID('municipio_customizer_section_header_panel_tab_menu')
                    ->setTitle(esc_html__('Tab menu', 'municipio'))
                    ->setFieldsCallback(fn() => new \Municipio\Customizer\Sections\Menu\Tabmenu('municipio_customizer_section_header_panel_tab_menu')),
            )
            ->addSection(
                CustomizerPanelSection::create()
                    ->setID('municipio_customizer_section_vertical')
                    ->setTitle(esc_html__('Vertical menu', 'municipio'))
                    ->setFieldsCallback(fn() => new \Municipio\Customizer\Sections\Menu\Vertical('municipio_customizer_section_vertical')),
            )
            ->register();
    }

    /* Hero — standalone top-level section */
    public static function registerHeroSection()
    {
        CustomizerPanelSection::create()
            ->setID('municipio_customizer_section_hero')
            ->setTitle(esc_html__('Hero', 'municipio'))
            ->setPriority(40)
            ->setFieldsCallback(fn() => new \Municipio\Customizer\Sections\Component\Hero('municipio_customizer_section_hero'))
            ->register();
    }

    /* Footer — standalone top-level section */
    public static function registerFooterSection()
    {
        CustomizerPanelSection::create()
            ->setID('municipio_customizer_section_component_footer')
            ->setTitle(esc_html__('Footer', 'municipio'))
            ->setPriority(45)
            ->setFieldsCallback(fn() => new \Municipio\Customizer\Sections\Component\Footer('municipio_customizer_section_component_footer'))
            ->register();
    }

    /* Cards — standalone top-level section */
    public static function registerCardsSection()
    {
        CustomizerPanelSection::create()
            ->setID('municipio_customizer_section_component_card')
            ->setTitle(esc_html__('Cards', 'municipio'))
            ->setPriority(50)
            ->setFieldsCallback(fn() => new \Municipio\Customizer\Sections\Component\Card('municipio_customizer_section_component_card'))
            ->register();
    }

    /* Slider panel */
    public static function registerSliderPanel()
    {
        CustomizerPanel::create()
            ->setID('municipio_customizer_section_component_slider')
            ->setTitle(esc_html__('Slider', 'municipio'))
            ->setPriority(55)
            ->addSection(
                CustomizerPanelSection::create()
                    ->setID('municipio_customizer_section_default_component_slider')
                    ->setTitle(esc_html__('Regular Slider', 'municipio'))
                    ->setFieldsCallback(fn() => new \Municipio\Customizer\Sections\Component\SliderDefault('municipio_customizer_section_default_component_slider')),
            )
            ->addSection(
                CustomizerPanelSection::create()
                    ->setID('municipio_customizer_section_hero_component_slider')
                    ->setTitle(esc_html__('Hero Slider', 'municipio'))
                    ->setDescription(esc_html__('Settings for the slider in the hero area.', 'municipio'))
                    ->setFieldsCallback(fn() => new \Municipio\Customizer\Sections\Component\SliderHero('municipio_customizer_section_hero_component_slider')),
            )
            ->register();
    }

    /* Date Badge — standalone top-level section */
    public static function registerDatebadgeSection()
    {
        CustomizerPanelSection::create()
            ->setID('municipio_customizer_section_datebadge')
            ->setTitle(esc_html__('Date Badge', 'municipio'))
            ->setPriority(60)
            ->setFieldsCallback(fn() => new \Municipio\Customizer\Sections\Component\Datebadge('municipio_customizer_section_datebadge'))
            ->register();
    }

    /* Divider — standalone top-level section */
    public static function registerDividerSection()
    {
        CustomizerPanelSection::create()
            ->setID('municipio_customizer_section_divider')
            ->setTitle(esc_html__('Divider', 'municipio'))
            ->setPriority(65)
            ->setFieldsCallback(fn() => new \Municipio\Customizer\Sections\Component\Divider('municipio_customizer_section_divider'))
            ->register();
    }

    /* Tags — standalone top-level section */
    public static function registerTagsSection()
    {
        CustomizerPanelSection::create()
            ->setID('municipio_customizer_section_tags')
            ->setTitle(esc_html__('Tags', 'municipio'))
            ->setPriority(70)
            ->setFieldsCallback(fn() => new \Municipio\Customizer\Sections\Component\Tags('municipio_customizer_section_tags'))
            ->register();
    }

    /* Open Street Map — standalone top-level section */
    public static function registerOpenStreetMapSection()
    {
        CustomizerPanelSection::create()
            ->setID('municipio_customizer_section_component_openstreetmap')
            ->setTitle(esc_html__('Maps', 'municipio'))
            ->setPriority(75)
            ->setFieldsCallback(fn() => new \Municipio\Customizer\Sections\Component\OpenStreetMap('municipio_customizer_section_component_openstreetmap'))
            ->register();
    }

    /* Archive panel */
    public static function registerArchivePanel()
    {
        $panelID = 'municipio_customizer_panel_archive';
        $archives = self::getArchives();
        $sections = array_map(function ($archive) use ($panelID) {
            $id = "{$panelID}_{$archive->name}";
            return CustomizerPanelSection::create()
                ->setID($id)
                ->setPanel($panelID)
                ->setTitle($archive->label)
                ->setFieldsCallback(fn() => new \Municipio\Customizer\Sections\Archive($id, $archive))
                ->setPreviewUrl(get_post_type_archive_link($archive->name));
        }, $archives);

        CustomizerPanel::create()
            ->setID($panelID)
            ->setTitle(esc_html__('Archives', 'municipio'))
            ->setDescription(esc_html__('Manage appearance options on archives.', 'municipio'))
            ->setPriority(80)
            ->addSections($sections)
            ->register();
    }

    /* Error pages panel */
    public static function registerErrorPagesPanel()
    {
        CustomizerPanel::create()
            ->setID('municipio_customizer_panel_error_pages_module')
            ->setTitle(esc_html__('Error Pages', 'municipio'))
            ->addSection(
                CustomizerPanelSection::create()
                    ->setID('municipio_customizer_section_error_401')
                    ->setTitle(esc_html__('401 Unauthorized', 'municipio'))
                    ->setFieldsCallback(fn() => new \Municipio\Customizer\Sections\ErrorPages('municipio_customizer_section_error_401', 401))
                    ->setPreviewUrl(self::getErrorPagePreviewUrl('401')),
            )
            ->addSection(
                CustomizerPanelSection::create()
                    ->setID('municipio_customizer_section_error_403')
                    ->setTitle(esc_html__('403 Forbidden', 'municipio'))
                    ->setFieldsCallback(fn() => new \Municipio\Customizer\Sections\ErrorPages('municipio_customizer_section_error_403', 403))
                    ->setPreviewUrl(self::getErrorPagePreviewUrl('403')),
            )
            ->addSection(
                CustomizerPanelSection::create()
                    ->setID('municipio_customizer_section_error_404')
                    ->setTitle(esc_html__('404 Not found', 'municipio'))
                    ->setFieldsCallback(fn() => new \Municipio\Customizer\Sections\ErrorPages('municipio_customizer_section_error_404', 404))
                    ->setPreviewUrl(self::getErrorPagePreviewUrl('404')),
            )
            ->register();
    }

    private static function getErrorPagePreviewUrl(string $type): string
    {
        return add_query_arg('municipio_error_preview', $type, home_url('/'));
    }

    public static function getArchivePanelSectionsConfiguaration(string $parentPanelID): array
    {
        $archives = self::getArchives();
        $archiveSections = [];

        if (is_array($archives) && !empty($archives)) {
            foreach ($archives as $archive) {
                $panelID = $parentPanelID . '_' . $archive->name;
                $archiveSections[] = [
                    'id' => $panelID,
                    'initFields' => fn() => new \Municipio\Customizer\Sections\Archive($panelID, $archive),
                    'args' => [
                        'title' => $archive->label,
                        'preview_url' => get_post_type_archive_link($archive->name),
                    ],
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
    private static function getArchives(array $excludedPostTypes = ['page', 'attachment']): array
    {
        $postTypes = array();

        foreach ((array) get_post_types() as $key => $postType) {
            $args = get_post_type_object($postType);

            if (!$args->public || in_array($args->name, $excludedPostTypes)) {
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
            'name' => 'author',
            'label' => __('Author'),
            'has_archive' => true,
            'is_author_archive' => true,
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
            'post_date' => 'Date published',
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
            'post_date' => 'Date published',
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
