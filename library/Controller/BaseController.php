<?php

namespace Municipio\Controller;

use WpService\WpService;
use AcfService\AcfService;
use Municipio\Helper\FormatObject;
use Municipio\Helper\TranslatedLabels;
use Municipio\Helper\Color;

// Menu
use Municipio\Controller\Navigation\Config\MenuConfig;
use Municipio\Controller\Navigation\MenuBuilderInterface;
use Municipio\Controller\Navigation\MenuDirector;
use Municipio\Helper\CurrentPostId;

/**
 * This class serves as the base controller for all controllers in the theme.
 */
class BaseController
{
    /**
     * Holds the view's data
     * @var array
     */
    public $data = [];

    /**
     * WordPress Global states
     * @var object
     */
    protected $wpQuery = null;

    /**
     * WordPress Posts object
     * @var object
     */
    protected $posts = null;

    /**
     * @var null $db The database connection object.
     */
    protected $db = null;

    /**
     * @var int $pageId The current page id.
     */
    protected ?int $pageId = null;

    /**
     * @var array $standardMenuDecorators The standard menu decorators.
     */
    protected array $standardMenuDecorators = [];

    /**
     * Init data fetching
     * @var object
     */
    public function __construct(
        protected MenuBuilderInterface $menuBuilder,
        protected MenuDirector $menuDirector,
        protected WpService $wpService,
        protected AcfService $acfService
    ) {
        //Store globals
        $this->globalToLocal('wp_query', 'wpQuery');
        $this->globalToLocal('posts');
        $this->globalToLocal('wpdb', 'db');

        //Send globals to view
        $this->data['wpQuery'] = $this->wpQuery;

        //Header & Footer
        $this->data['wpHeader'] = $this->getWpHeader();
        $this->data['wpFooter'] = $this->getWpFooter();

        //Basic
        $this->data['ajaxUrl']            = $this->getAjaxUrl();
        $this->data['bodyClass']          = $this->getBodyClass();
        $this->data['languageAttributes'] = $this->getLanguageAttrs();
        $this->data['homeUrl']            = $this->getHomeUrl();
        $this->data['adminUrl']           = $this->getAdminUrl();
        $this->data['homeUrlPath']        = parse_url(get_home_url(), PHP_URL_PATH);
        $this->data['siteName']           = $this->getSiteName();


        //View porperties
        $this->data['isFrontPage'] = is_front_page() || is_home() ? true : false;
        $this->data['isSingular']  = is_singular();
        $this->data['isSingle']    = is_single();
        $this->data['isSticky']    = is_sticky();

        $this->data['hasBlocks'] = $this->hasBlocks();

        //Post data
        $this->data['pageTitle']     = $this->getPageTitle();
        $this->data['pagePublished'] = $this->getPagePublished();
        $this->data['pageModified']  = $this->getPageModified();
        $this->data['pageID']        = CurrentPostId::get();
        $this->data['pageParentID']  = $this->getPageParentID();

        //Customization data
        $this->data['customizer'] = apply_filters('Municipio/Controller/Customizer', []);

        //Logotypes
        $this->data['logotype']           = $this->getLogotype($this->data['customizer']->headerLogotype ?? 'standard', true);
        $this->data['footerLogotype']     = $this->getLogotype($this->data['customizer']->footerLogotype ?? 'negative');
        $this->data['subfooterLogotype']  = $this->getSubfooterLogotype($this->data['customizer']->footerSubfooterLogotype ?? false);
        $this->data['emblem']             = $this->getEmblem();
        $this->data['showEmblemInHero']   = $this->data['customizer']->showEmblemInHero ?? true;
        $this->data['brandText']          = $this->getMultilineTextAsArray(get_option('brand_text', ''));
        $this->data['headerBrandEnabled'] = $this->data['customizer']->headerBrandEnabled && !empty($this->data['brandText']);

        // Footer
        [$footerStyle, $footerColumns, $footerAreas] = $this->getFooterSettings();
        $this->data['footerColumns']                 = $footerColumns;
        $this->data['footerGridSize']                = $footerStyle === 'columns' ? floor(12 / $footerColumns) : 12;
        $this->data['footerAreas']                   = $footerAreas;
        $this->data['footerTextAlignment']           = $this->data['customizer']->municipioCustomizerSectionComponentFooterMain['footerTextAlignment'];

        // Header controllers
        if (isset($this->data['customizer']->headerApperance)) {
            $headerClassName = '\Municipio\Controller\Header\\' . ucfirst($this->data['customizer']->headerApperance);
            if (class_exists($headerClassName)) {
                $headerController = new $headerClassName($this->data['customizer']);
            }
        }

        //User login logout
        $this->data['customizer']->headerLoginLogoutBackgroundColorIsVisible = Color::isRgbaVisible(
            $this->data['customizer']->headerLoginLogoutBackgroundColor ?? null
        );

        $this->data['headerData'] = isset($headerController) ? $headerController->getHeaderData() : [];

        $this->menuDirector->setBuilder($this->menuBuilder);

        // Accessibility menu
        $accessibilityMenuConfig = new MenuConfig(
            'accessibility',
            '',
        );

        $this->menuBuilder->setConfig($accessibilityMenuConfig);
        $this->menuDirector->buildAccessibilityMenu();
        $this->data['accessibilityMenu'] = $this->menuBuilder->getMenu()->getMenu();

        // Breadcrumb menu
        $breadcrumbMenuConfig = new MenuConfig(
            'breadcrumb',
            '',
        );

        $this->menuBuilder->setConfig($breadcrumbMenuConfig);
        $this->menuDirector->buildBreadcrumbMenu();
        $this->data['breadcrumbMenu'] = $this->menuBuilder->getMenu()->getMenu();

        // Mobile menu
        $mobileMenuConfig = new MenuConfig(
            'mobile',
            'secondary-menu',
            false,
            false,
            \Kirki::get_option('mobile_menu_pagetree_fallback')
        );

        $this->menuBuilder->setConfig($mobileMenuConfig);
        $mobileMenuConfig->getFallbackToPageTree() ?
            $this->menuDirector->buildMixedPageTreeMenu(true) :
            $this->menuDirector->buildStandardMenu();
        $this->data['mobileMenu'] = $this->menuBuilder->getMenu()->getMenu();

        // Primary menu
        $primaryMenuConfig = new MenuConfig(
            'primary',
            'main-menu',
            !$this->data['customizer']->primaryMenuDropdown,
            false,
            \Kirki::get_option('primary_menu_pagetree_fallback')
        );

        $this->menuBuilder->setConfig($primaryMenuConfig);
        $primaryMenuConfig->getFallbackToPageTree() ?
            $this->menuDirector->buildStandardWithPageTreeFallbackMenu() :
            $this->menuDirector->buildStandardMenu();
        $this->data['primaryMenu'] = $this->menuBuilder->getMenu()->getMenu();

        // Mobile secondary menu
        $mobileMenuSecondaryConfig = new MenuConfig(
            'mobile-secondary',
            'mobile-drawer',
        );

        $this->menuBuilder->setConfig($mobileMenuSecondaryConfig);
        $this->menuDirector->buildStandardMenu();
        $this->data['mobileSecondaryMenu'] = $this->menuBuilder->getMenu()->getMenu();

        // Mega menu
        $megaMenuConfig = new MenuConfig(
            'mega-menu',
            'mega-menu',
            false,
            false,
            \Kirki::get_option('mega_menu_pagetree_fallback')
        );

        $this->menuBuilder->setConfig($megaMenuConfig);
        $megaMenuConfig->getFallbackToPageTree() ?
            $this->menuDirector->buildMixedPageTreeMenu() :
            $this->menuDirector->buildStandardMenu();
        $this->data['megaMenu'] = $this->menuBuilder->getMenu()->getMenu();

        $quicklinksMenuConfig = new MenuConfig(
            'quicklinks',
            'quicklinks-menu',
            true
        );

        // Quicklinks menu
        $this->menuBuilder->setConfig($quicklinksMenuConfig);
        $this->menuDirector->buildStandardMenu();
        $this->data['quicklinksMenu'] = $this->menuBuilder->getMenu()->getMenu();

        // Tab menu
        $tabMenuConfig = new MenuConfig(
            'tab',
            'header-tabs-menu',
        );

        $this->menuBuilder->setConfig($tabMenuConfig);
        $this->menuDirector->buildStandardMenu();
        $this->data['tabMenu'] = $this->menuBuilder->getMenu()->getMenu();

        // Help menu
        // TODO: Find out what it does
        $helpMenuConfig = new MenuConfig(
            'help',
            'help-menu',
        );

        $this->menuBuilder->setConfig($helpMenuConfig);
        $this->menuDirector->buildStandardMenu();
        $this->data['helpMenuItems'] = $this->menuBuilder->getMenu()->getMenu()['items'];

        // Dropdown menu
        // TODO: Find out what it does
        $dropdownMenuConfig = new MenuConfig(
            'dropdown',
            'dropdown-links-menu',
        );

        $this->menuBuilder->setConfig($dropdownMenuConfig);
        $this->menuDirector->buildStandardMenu();
        $this->data['dropdownMenuItems'] = $this->menuBuilder->getMenu()->getMenu()['items'];

        // Floating menu
        $floatingMenuConfig = new MenuConfig(
            'floating',
            'floating-menu',
            true,
        );

        $this->menuBuilder->setConfig($floatingMenuConfig);
        $this->menuDirector->buildStandardMenu();
        $this->data['floatingMenu'] = $this->menuBuilder->getMenu()->getMenu();

        // Language menu
        $languageMenuConfig = new MenuConfig(
            'language',
            'language-menu',
        );

        $this->menuBuilder->setConfig($languageMenuConfig);
        $this->menuDirector->buildStandardMenu();
        $this->data['languageMenu'] = $this->menuBuilder->getMenu()->getMenu();

        // Site selector menu
        $siteselectorMenuConfig = new MenuConfig(
            'siteselector',
            'siteselector-menu',
            true,
        );

        $this->menuBuilder->setConfig($siteselectorMenuConfig);
        $this->menuDirector->buildStandardMenu();
        $this->data['siteselectorMenu'] = $this->menuBuilder->getMenu()->getMenu();

        // Sidebar menu
        $secondaryMenuPostTypeConfig = new MenuConfig(
            'sidebar',
            $this->wpService->getPostType() . '-secondary-menu',
        );

        $secondaryMenuConfig = new MenuConfig(
            'sidebar',
            'secondary-menu',
            false,
            empty($this->data['primaryMenu']['items']) ? false : true,
            \Kirki::get_option('secondary_menu_pagetree_fallback'),
        );

        $this->menuBuilder->setConfig($secondaryMenuPostTypeConfig);
        $this->menuDirector->buildStandardMenu();
        $secondaryMenu = $this->menuBuilder->getMenu()->getMenu();

        if (empty($secondaryMenu['items'])) {
            $this->menuBuilder->setConfig($secondaryMenuConfig);
            $secondaryMenuConfig->getFallbackToPageTree() ?
                $this->menuDirector->buildMixedPageTreeMenu() :
                $this->menuDirector->buildStandardMenu();
            $secondaryMenu = $this->menuBuilder->getMenu()->getMenu();
        }

        $this->data['secondaryMenu'] = $secondaryMenu;

        //Helper nav placement
        $this->data['helperNavBeforeContent'] = apply_filters('Municipio/Partials/Navigation/HelperNavBeforeContent', true);

        //Get labels for menu
        $this->data['floatingMenuLabels'] = $this->getFloatingMenuLabels();
        $this->data['quicklinksOptions']  = $this->getQuicklinksOptions();
        $this->data['megaMenuLabels']     = $this->getmegaMenuLabels();
        //Get language menu options
        $this->data['languageMenuOptions'] = $this->getLanguageMenuOptions();

        // Show sidebars if not set to false in template controllers
        $this->data['showSidebars'] = true;

        // Get date & time formats
        $this->data['dateTimeFormat'] = \Municipio\Helper\DateFormat::getDateFormat('date-time');
        $this->data['dateFormat']     = \Municipio\Helper\DateFormat::getDateFormat('date');
        $this->data['timeFormat']     = \Municipio\Helper\DateFormat::getDateFormat('time');

        //User is authenticated
        $this->data['user']                 = $this->wpService->wpGetCurrentUser();
        $this->data['isAuthenticated']      = $this->wpService->isUserLoggedIn();
        $this->data['isAdminBarShowing']    = $this->wpService->isAdminBarShowing();
        $this->data['loginUrl']             = $this->wpService->wpLoginUrl();
        $this->data['logoutUrl']            = $this->wpService->wpLogoutUrl();

        //User role
        $this->data['userRole'] = $this->getUserRole();  //TODO: MOVE TO USER HELPER CLASS

        //Show admin notices
        $this->data['showAdminNotices'] = $this->showAdminNotices(); //TODO: MOVE TO USER HELPER CLASS

        //Search
        $this->data['showHeaderSearch']       = $this->showSearchForm('header');
        $this->data['showNavigationSearch']   = $this->showSearchForm('navigation');
        $this->data['showQuicklinksSearch']   = $this->showSearchForm('quicklinks');
        $this->data['showMegaMenuSearch']     = $this->showSearchForm('mega-menu');
        $this->data['showHeroSearch']         = $this->showSearchForm('hero');
        $this->data['showMobileSearch']       = $this->showSearchForm('mobile');
        $this->data['showMobileSearchDrawer'] = $this->showSearchForm('mobile-drawer');
        $this->data['searchQuery']            = get_search_query();

        // Current posttype
        $this->data['postTypeDetails'] = \Municipio\Helper\PostType::postTypeDetails();
        $this->data['postType']        = $this->data['postTypeDetails']->name ?? '';

        // Get page template
        $this->data['pageTemplate'] = $this->getPageTemplate();

        // Skip links
        $this->data['skipToMainContentLink'] = $this->setSkipLinkValue();
        $this->data['hasSideMenu']           = $this->hasSideMenu();
        $this->data['hasMainMenu']           = $this->hasMainMenu();

        $this->data['structuredData'] = \Municipio\Helper\Data::normalizeStructuredData([]);
        //Notice storage
        $this->data['notice'] = [];

        //Column sizes
        $this->data['leftColumnSize']  = $this->getColumnSize('left', $this->data['customizer']);
        $this->data['rightColumnSize'] = $this->getColumnSize('right', $this->data['customizer']);

        //Main content padder
        $this->data['mainContentPadding'] = ['md' => 0, 'lg' => 0]; //Used to define view vars, used in singular controller.

        //Language
        $this->data['lang'] = TranslatedLabels::getLang(
            [
                'searchFor' => ucfirst(strtolower($this->data['postTypeDetails']->labels->search_items ?? __('Search for content', 'municipio'))),
                'noResult'  => $this->data['postTypeDetails']->labels->not_found ?? __('No items found at this query.', 'municipio'),
                'logout'    => __('Logout', 'municipio'),
                'login'     => __('Login', 'municipio'),
            ]
        );

            $this->data['labels'] = (array) $this->data['lang'];

            add_filter('ComponentLibrary/Component/Lang', function ($obj) {
                $lang = [
                    'visit' => __('Visit', 'municipio'),
                ];

                return (object) array_merge((array) $obj, $lang);
            }, 10, 1);

        if (!empty($_SERVER['HTTP_HOST'])) {
            add_filter("ComponentLibrary/Component/Attribute", function ($attributes) {
                if (!empty($attributes['href'])) {
                    $parsedUrl = parse_url($attributes['href']);

                    if (!empty($parsedUrl['host']) && $parsedUrl['host'] !== $_SERVER['HTTP_HOST']) {
                        $attributes['data-js-original-link'] = $attributes['href'];
                    }
                }

                return $attributes;
            }, 10, 1);
        }

            //Wordpress hooks
            $this->data['hook'] = (object) array(
                'innerLoopStart'       => $this->hook('inner_loop_start'),
                'innerLoopEnd'         => $this->hook('inner_loop_end'),
                'articleContentBefore' => $this->hook('article_content_before'),
                'loopStart'            => $this->hook('loop_start'),
                'loopEnd'              => $this->hook('loop_end'),
                'secondaryLoopStart'   => $this->hook('secondary_loop_start'),
                'secondaryLoopEnd'     => $this->hook('secondary_loop_end')
            );

            //Quicklinks placement is set in Singular
            $this->data['displayQuicklinksAfterContent'] = false;

            // Add filters to add emblem on blocks and cards with placeholders
            add_filter('ComponentLibrary/Component/Icon/Data', [$this, 'componentDataEmblemFilter'], 10, 1);

            $googleTranslate = new \Municipio\Helper\GoogleTranslate();

            $this->init();
    }

    /**
     * Get the emblem to use
     *
     * @param array $data
     * @return array
     */
    public function componentDataEmblemFilter($data)
    {
        $contexts = isset($data['context']) ? (array) $data['context'] : [];
        if (in_array('component.image.placeholder.icon', $contexts)) {
            $data['icon'] = $this->getEmblem();
        }
        return $data;
    }

    /**
     * Run WordPress hooks
     *
     * @param string $hookKey
     *
     * @return mixed Returns the output of the hook, mixed values.
     */
    public function hook($hookKey): string
    {
        ob_start();
        do_action($hookKey);
        return apply_filters('Municipio/Hook/' . \Municipio\Helper\FormatObject::camelCase($hookKey), ob_get_clean());
    }

    /**
     * Get WordPress site name
     *
     * @return string
     */
    public function getSiteName(): string
    {
        return apply_filters('Municipio/SiteName', get_bloginfo('name'));
    }

    /**
     * Get WordPress header
     *
     * @return string
     */
    public function getWpHeader(): string
    {
        ob_start();
        wp_head();
        return apply_filters('Municipio/HeaderHTML', ob_get_clean());
    }

    /**
     * Get WordPress footer
     *
     * @return string
     */
    public function getWpFooter(): string
    {
        ob_start();
        wp_footer();
        return apply_filters('Municipio/FooterHTML', ob_get_clean());
    }

    /**
     * Get current page ID
     */
    public function getPageID(): int
    {
        return CurrentPostId::get();
    }

    /**
     * Get current parent ID
     *
     * @return integer
     */
    public function getPageParentID(): int
    {
        return wp_get_post_parent_id(CurrentPostId::get());
    }

    /**
     * Should show admin notices
     */
    public function showAdminNotices()
    {
        if (is_user_logged_in() && current_user_can('edit_themes')) {
            return true;
        }
        return false;
    }

    /**
     * Detect use of gutenberg editor on current page
     */
    public function hasBlocks()
    {
        //Backwards compability with old versions of wp
        if (!function_exists('has_blocks')) {
            return false;
        }

        return has_blocks(CurrentPostId::get());
    }

    /**
     * Get floating menu labels
     *
     * @return object
     */
    public function getFloatingMenuLabels(): object
    {
        $menuObject = wp_get_nav_menu_object(get_nav_menu_locations()['floating-menu'] ?? '');

        return (object) apply_filters(
            'Municipio/FloatingMenuLabels',
            [
            'heading'     => get_field('floating_popup_heading', $menuObject),
            'buttonLabel' => get_field('floating_toggle_button_label', $menuObject),
            'buttonIcon'  => get_field('toggle_button_icon', $menuObject)
            ]
        );
    }

    /**
     * Get mega menu labels
     *
     * @return object
     */
    public function getmegaMenuLabels(): object
    {
        $menuObject = wp_get_nav_menu_object(get_nav_menu_locations()['mega-menu'] ?? '');

        return (object) apply_filters(
            'Municipio/MegaMenuLabels',
            [
                'buttonLabel'    => get_field('mega_menu_button_label', $menuObject),
                'buttonIcon'     => get_field('mega_menu_button_icon', $menuObject),
                'iconAfterLabel' => get_field('mega_menu_icon_after_label', $menuObject)
            ]
        );
    }

    /**
     * Get language menu options
     *
     * @return object
     */
    public function getLanguageMenuOptions(): object
    {
        $options = wp_get_nav_menu_object(get_nav_menu_locations()['language-menu'] ?? '');

        $options = [
        'disclaimer'       => get_field('language_menu_disclaimer', $options),
        'moreLanguageLink' => get_field('language_menu_more_languages', $options)
        ];

        return (object) $options;
    }

    /**
     * Get quicklinks menu options
     *
     * @return object
     */
    public function getQuicklinksOptions(): object
    {
        $options = wp_get_nav_menu_object(get_nav_menu_locations()['quicklinks-menu'] ?? '');

        $options = [
        'backgroundColor' => get_field('quicklinks_background_color', $options),
        'textColor'       => get_field('quicklinks_text_color', $options)
        ];

        return (object) $options;
    }


    /**
     * Get current user role
     * @return mixed    String or false with role
     */
    public function getUserRole()
    {
        //Check login
        if (!is_user_logged_in()) {
            return false;
        }

        //Return user role
        if ($userRoles = wp_get_current_user()->roles) {
            if (is_array($userRoles) && !empty($userRoles)) {
                return array_pop($userRoles);
            }
        }

        return false;
    }

    /**
      * Get page template
      */
    public function getPageTemplate()
    {
        $type = get_page_template_slug();
        if ($type === '') {
            return false;
        }
        return $type;
    }

    /**
      * Create skip to main content link
      */
    protected function setSkipLinkValue()
    {
        if ($this->data['pageTemplate'] === 'one-page.blade.php') {
            return apply_filters('Municipio/Controller/SkipToMainContentLinkOnePage', '#main-content');
        }
        return apply_filters('Municipio/Controller/SkipToMainContentLinkDefaultValue', '#article');
    }

    /**
      * Check if page has side menu
      */
    protected function hasSideMenu()
    {
        if (!empty($this->data['secondaryMenu']['items']) && $this->data['pageTemplate'] !== 'one-page.blade.php') {
            return true;
        }
        return false;
    }

    /**
      * Check if page has main menu
      */
    protected function hasMainMenu()
    {
        if (!empty($this->data['primaryMenu']['items'])) {
            return true;
        }
        return false;
    }

    /**
     * Check if any posts in the given array has an image
     */
    protected function anyPostHasImage(array $posts)
    {
        foreach ($posts as $post) {
            if (!empty($post->images['thumbnail16:9']['src'])) {
                return true;
            }
        }
        return false;
    }

    /**
     * Retrieves the footer settings.
     *
     * @return array An array containing the footer style, number of footer columns, and footer areas.
     */
    protected function getFooterSettings()
    {
        $footerStyle   = $this->data['customizer']->municipioCustomizerSectionComponentFooterMain['footerStyle'];
        $footerAreas   = ['footer-area'];
        $footerColumns = 1;
        if ($footerStyle === 'columns') {
            $footerColumns = $this->data['customizer']->municipioCustomizerSectionComponentFooterMain['footerColumns'];
            for ($i = 1; $i < $footerColumns; $i++) {
                $footerAreas[] = 'footer-area-column-' . $i;
            }
        }

        return [$footerStyle, $footerColumns, $footerAreas];
    }

    /**
     * Determine if search boxes should be displayed
     *
     * @param string $location
     * @return boolean
     */
    protected function showSearchForm($location = null)
    {
        if (!isset($this->data['customizer']->searchDisplay)) {
            return true;
        }

        $enabledLocations = $this->data['customizer']->searchDisplay;

        if ($location == "hero" && is_front_page()) {
            return in_array($location, $enabledLocations);
        }

        if ($location == "mobile") {
            //Do not show on frontpage, if hero search is active
            if (!in_array("hero", $enabledLocations) && is_front_page()) {
                return true;
            }

            //Show if not frontpage, not search and search is enabled anywhere else.
            if (!is_front_page() && !is_search() && !empty($enabledLocations)) {
                return true;
            }
        }

        if ($location == "mobile-drawer" && $this->data['customizer']->headerApperance !== 'business') {
            if ($this->showSearchForm('mobile')) {
                return true;
            }
        }

        if ($location == "header") {
            if (is_search()) {
                return false;
            }

            if (is_front_page()) {
                return in_array('header', $enabledLocations);
            }

            if (!is_front_page()) {
                return in_array('header_sub', $enabledLocations);
            }
        }

        if ($location == "navigation") {
            if (is_search()) {
                return false;
            }

            return in_array('mainmenu', $enabledLocations);
        }

        if ($location == "mega-menu") {
            return in_array('mega_menu', $enabledLocations);
        }

        if ($location == "quicklinks") {
            return in_array('quicklinks', $enabledLocations);
        }

        return false;
    }

    /**
     * Get getPostTypeDetails
     * @return string
     */
    protected function getPostTypeDetails(): object
    {
        return apply_filters('Municipio/postTypeDetails', (object) get_post_type_object(get_post_type()));
    }

    /**
     * Get home url
     * @return string
     */
    protected function getHomeUrl(): string
    {
        return apply_filters('Municipio/homeUrl', esc_url(get_home_url()));
    }

    /**
     * Get admin url
     * @return string
     */
    protected function getAdminUrl(): string
    {
        return apply_filters('Municipio/adminUrl', get_admin_url());
    }

    /**
     * Get post published
     * @return string
     */
    protected function getPagePublished(): string
    {
        return apply_filters('Municipio/postPublished', get_the_time('Y-m-d'));
    }

    /**
     * Get post modified
     * @return string
     */
    protected function getPageModified(): string
    {
        return apply_filters('Municipio/postModified', get_the_modified_time('Y-m-d'));
    }

    /**
     * Get language attributes
     * @return string
     */
    protected function getBlogDescription(): string
    {
        return apply_filters('Municipio/blogDescription', get_bloginfo('description'));
    }

    /**
     * Get post title
     * @return string
     */
    protected function getPageTitle(): string
    {
        return apply_filters('Municipio/postTitle', wp_title('|', false, 'right'));
    }

    /**
     * Get language attributes
     * @return string
     */
    protected function getLanguageAttrs(): string
    {
        return apply_filters_deprecated('Municipio/language_attributes', array(get_language_attributes()), "3.0", "Municpio/languageAttributes");
    }

    /**
     * Creates a ajax url
     * @return string
     */
    protected function getAjaxUrl(): string
    {
        return apply_filters_deprecated('Municipio/ajax_url_in_head', array(admin_url('admin-ajax.php')), "3.0", "Municpio/ajaxUrl");
    }

    /**
     * Get body class
     * @return string
     */
    protected function getBodyClass(): string
    {
        return apply_filters('Municipio/bodyClass', join(' ', get_body_class('no-js')));
    }

    /**
     * Get breadcrumb array
     * @return array
     */
    protected function getBreadcrumb(): array
    {
        return apply_filters('Municipio/breadcrumbArray', breadcrumb());
    }

    /**
     * Get size of column
     *
     * @return integer
     */
    public function getColumnSize($location, $customizer)
    {
        if ($location == 'left' && $customizer->columnSizeLeft == 'large') {
            return 4;
        }

        if ($location == 'right' && $customizer->columnSizeRight == 'large') {
            return 4;
        }

        return 3;
    }

    /**
     * Get emblem svg
     *
     * @return bool|string
     */
    public function getEmblem()
    {
        if (empty($logotypeEmblem = $this->data['customizer']->logotypeEmblem)) {
            return false;
        }

        return $logotypeEmblem;
    }

    /**
     * Get the logotype url.
     *
     * @param string $variant
     * @return string Logotype file url, defaults to the theme logo if not found.
     */
    public function getLogotype($variant = "standard", $fallback = false): string
    {
        $variantKey = "logotype";

        if ($variant !== "standard" && !is_null($variant)) {
            $variantKey = FormatObject::camelCaseString("{$variantKey}_{$variant}");
        }

        $logotypeUrl = isset($this->data['customizer']->$variantKey)
            ? $this->data['customizer']->{$variantKey}
            : '';

        if (empty($logotypeUrl) && $fallback) {
            return $this->getDefaultLogotype();
        }

        return $logotypeUrl;
    }

    /**
     * Returns the default logotype.
     *
     * @return string The URL of the default logotype image.
     */
    public function getDefaultLogotype(): string
    {
        return get_stylesheet_directory_uri() . '/assets/images/municipio.svg';
    }

    /**
     * Returns a multiline text as an array.
     *
     * @param string $text The multiline text to convert to an array.
     * @return array|null The array representation of the multiline text, or null if the text is empty.
     */
    public function getMultilineTextAsArray(string $text)
    {
        $trimmed = trim($text);

        if (empty($trimmed)) {
            return null;
        }

        return explode("\n", $trimmed);
    }

    /**
     * Get the subfooter logotype
     *
     * @param string $variant
     * @return string|boolean
     */
    public function getSubfooterLogotype($variant = "standard")
    {
        if (!$variant) {
            return false;
        }

        if ($variant === 'custom') {
            return $this->data['customizer']->footerSubfooterCustomLogotype;
        }

        return $this->getLogotype($variant) ?? false;
    }
    /**
     * Runs after construct
     * @return void
     */
    public function init()
    {
        do_action('Municipio/Controller/Init');
    }

    /**
     * Returns the data
     * @return array Data
     */
    public function getData()
    {
        //Create filters for all data vars
        if (isset($this->data) && !empty($this->data) && is_array($this->data)) {
            foreach ($this->data as $key => $value) {
                $this->data[$key] = apply_filters('Municipio/' . $key, $value);
            }
        }

        //Old depricated filter
        $this->data = apply_filters_deprecated('HbgBlade/data', array($this->data), "2.0", "Municipio/viewData");

        //General filter
        return $this->data;
    }

    /**
     * Creates a local copy of the global instance
     * The target var should be defined in class header as private or public
     * @param string $global The name of global varable that should be made local
     * @param string $local Handle the global with the name of this string locally
     * @return void
     */
    public function globalToLocal($global, $local = null)
    {
        global $$global;

        if (is_null($$global)) {
            return false;
        }

        if (is_null($local)) {
            $this->$global = $$global;
        } else {
            $this->$local = $$global;
        }

        return true;
    }
}
