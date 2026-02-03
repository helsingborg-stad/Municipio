<?php

namespace Municipio\Controller;

use WpService\WpService;
use AcfService\AcfService;
use Municipio\Helper\FormatObject;
use Municipio\Helper\TranslatedLabels;
use Municipio\Helper\User\User;
use Municipio\Controller\Navigation\Config\MenuConfig;
use Municipio\Controller\Navigation\MenuBuilderInterface;
use Municipio\Controller\Navigation\MenuDirector;
use Municipio\Helper\CurrentPostId;
use Municipio\Helper\SiteSwitcher\SiteSwitcherInterface;

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
        protected AcfService $acfService,
        protected SiteSwitcherInterface $siteSwitcher,
        protected User $userHelper
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
        $this->data['homeUrlPath']        = $this->getHomeUrlPath();
        $this->data['siteName']           = $this->getSiteName();

        // Feeds
        $this->data['rssFeedUrls']        = $this->getAllPublicPostTypeRssFeeds();

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
        $brandTextOption = get_option('brand_text', '');
        $this->data['brandText']          = $this->getMultilineTextAsArray(is_string($brandTextOption) ? $brandTextOption : '');
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
        $kirkiMobileFallback = class_exists('Kirki') ? \Kirki::get_option('mobile_menu_pagetree_fallback') : false;
        $mobileMenuConfig = new MenuConfig(
            'mobile',
            'secondary-menu',
            false,
            false,
            $kirkiMobileFallback
        );

        $this->menuBuilder->setConfig($mobileMenuConfig);
        $mobileMenuConfig->getFallbackToPageTree() ?
            $this->menuDirector->buildMixedPageTreeMenu(true) :
            $this->menuDirector->buildStandardMenu();
        $this->data['mobileMenu'] = $this->menuBuilder->getMenu()->getMenu();

        // Primary menu
        $kirkiPrimaryFallback = class_exists('Kirki') ? \Kirki::get_option('primary_menu_pagetree_fallback') : false;
        $primaryMenuConfig = new MenuConfig(
            'primary',
            'main-menu',
            isset($this->data['customizer']->primaryMenuDropdown) ? !$this->data['customizer']->primaryMenuDropdown : false,
            false,
            $kirkiPrimaryFallback
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
        if(class_exists('Kirki')) {
            $megaMenuFallbackOption = \Kirki::get_option('mega_menu_pagetree_fallback');
        } else {
            $megaMenuFallbackOption = false;
        }
        $megaMenuConfig = new MenuConfig(
            'mega-menu',
            'mega-menu',
            false,
            false,
            $megaMenuFallbackOption
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

        if(class_exists('Kirki')) {
            $fallbackToPageTree = \Kirki::get_option('secondary_menu_pagetree_fallback');
        } else {
            $fallbackToPageTree = false;
        }
        $fallbackToPageTree = false;
        $secondaryMenuConfig = new MenuConfig(
            'sidebar',
            'secondary-menu',
            false,
            empty($this->data['primaryMenu']['items']) ? false : true,
            $fallbackToPageTree,
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
        $this->data['user']              = $this->wpService->wpGetCurrentUser();
        $this->data['isAuthenticated']   = $this->wpService->isUserLoggedIn();
        $this->data['isAdminBarShowing'] = $this->wpService->isAdminBarShowing();
        $this->data['loginUrl']          = $this->wpService->wpLoginUrl(
            $this->getCurrentUrl(['loggedin' => 'true'])
        );
        $this->data['logoutUrl']         = $this->wpService->wpLogoutUrl(
            $this->getCurrentUrl(['loggedout' => 'true'])
        );

        // User basic details
        $this->data['userDetails'] = $this->getUserDetails();

        //User role
        $this->data['userRole'] = $this->getUserRole();  //TODO: MOVE TO USER HELPER CLASS

        //User group
        $this->data['userGroup'] = (
            $this->wpService->isUserLoggedIn()
        ) ? (object) [
            'group'     => $this->userHelper->getUserGroup(),
            'url'       => $this->userHelper->getUserGroupUrl(),
            'shortname' => $this->userHelper->getUserGroupShortname()
        ] : null;

        //Show admin notices
        $this->data['showAdminNotices'] = $this->showAdminNotices(); //TODO: MOVE TO USER HELPER CLASS

        //Search
        $this->data['showHeaderSearchDesktop'] = $this->showSearchForm('header');
        $this->data['showHeaderSearchMobile']  = $this->showSearchForm('mobile');
        $this->data['showNavigationSearch']    = $this->showSearchForm('navigation');
        $this->data['showQuicklinksSearch']    = $this->showSearchForm('quicklinks');
        $this->data['showMegaMenuSearch']      = $this->showSearchForm('mega-menu');
        $this->data['showHeroSearch']          = $this->showSearchForm('hero');
        $this->data['showMobileSearchDrawer']  = $this->showSearchForm('mobile-drawer');
        $this->data['searchQuery']             = get_search_query();

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
                'close'     => __('Close', 'municipio'),
            ]
        );

            $this->data['labels'] = (array) $this->data['lang'];

            add_filter('ComponentLibrary/Component/Lang', function ($obj) {
                $lang = [
                    'visit'           => __('Visit', 'municipio'),
                    'email'           => __('Email', 'municipio'),
                    'call'            => __('Call', 'municipio'),
                    'address'         => __('Address', 'municipio'),
                    'visitingAddress' => __('Visiting address', 'municipio'),
                ];

                return (object) array_merge((array) $obj, $lang);
            }, 10, 1);

        if (!empty($_SERVER['HTTP_HOST'])) {
            add_filter("ComponentLibrary/Component/Attribute", function ($attributes) {
                if (!empty($attributes['href'])) {
                    $parsedUrl = parse_url($attributes['href']);

                    if (!empty($parsedUrl['host']) && $parsedUrl['host'] !== (empty($_SERVER['HTTP_HOST']) ? '' : $_SERVER['HTTP_HOST'])) {
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
                'articleContentAfter'  => $this->hook('article_content_after'),
                'loopStart'            => $this->hook('loop_start'),
                'loopEnd'              => $this->hook('loop_end'),
                'secondaryLoopStart'   => $this->hook('secondary_loop_start'),
                'secondaryLoopEnd'     => $this->hook('secondary_loop_end')
            );

            $this->data['quicklinksPlacement'] = $this->wpService->applyFilters(
                'Municipio/QuickLinksPlacement',
                $this->acfService->getField('quicklinks_placement', $this->data['pageID']),
                $this->data['pageID']
            );

            // Add filters to add emblem on blocks and cards with placeholders
            add_filter('ComponentLibrary/Component/Icon/Data', [$this, 'componentDataEmblemFilter'], 10, 1);

            $googleTranslate = new \Municipio\Helper\GoogleTranslate();

            $this->init();
    }

    /**
     * Get the current user details
     *
     * @return object
     */
    private function getUserDetails(): ?object
    {
        $user = $this->wpService->wpGetCurrentUser();

        if (!$user) {
            return null;
        }

        return (object) [
            'id'          => $user->ID,
            'email'       => $user->user_email,
            'displayname' => $user->display_name,
            'firstname'   => $user->first_name,
            'lastname'    => $user->last_name,
        ];
    }

    /**
     * Get the current URL with optional query parameters.
     *
     * @param array $queryParam Key-value pairs to add or override in the query string.
     * @return string The full URL.
     */
    private function getCurrentUrl(array $queryParam = []): string
    {
        $permalink = urldecode($this->wpService->getPermalink(\Municipio\Helper\CurrentPostId::get()));
        $permalink = add_query_arg($queryParam, $permalink);
        return urldecode($permalink);
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
            $data['label'] = __('Emblem', 'municipio');
            $data['icon']  = $this->getEmblem();
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
        $siteName = get_bloginfo('name');
        if (!is_string($siteName) || $siteName === null) {
            $siteName = '';
        }
        $filtered = apply_filters('Municipio/SiteName', $siteName);
        return is_string($filtered) ? $filtered : '';
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
        $header = apply_filters('Municipio/HeaderHTML', ob_get_clean());
        return is_string($header) ? $header : '';
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
        $footer = apply_filters('Municipio/FooterHTML', ob_get_clean());
        return is_string($footer) ? $footer : '';
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
        $parentId = wp_get_post_parent_id(CurrentPostId::get());
        return is_int($parentId) ? $parentId : 0;
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
            'disclaimer'             => get_field('language_menu_disclaimer', $options),
            'moreLanguageLink'       => get_field('language_menu_more_languages', $options),
            'displayCurrentLanguage' => get_field('display_current_language', $options) ?? false,
            'currentLanguage'        => null,
        ];

        // IF displayCurrentLanguage is set to true, we will get the current language
        if ($options['displayCurrentLanguage'] === true) {
            $locale                     = \get_locale();
            $getCurrentLang             = fn() => class_exists('Locale')
                ? (function_exists('mb_ucfirst')
                    ? mb_ucfirst(\Locale::getDisplayLanguage($locale, $locale))
                    : ucfirst(\Locale::getDisplayLanguage($locale, $locale)))
                : $locale;
            $options['currentLanguage'] = $getCurrentLang();
        }

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
        $customizer       = $this->data['customizer'] ?? null;
        $enabledLocations = $customizer->searchDisplay ?? null;

        // Return true if no customizer data exists
        if (is_null($customizer) || empty($enabledLocations)) {
            return false;
        }

        switch ($location) {
            case 'hero':
                return is_front_page() && in_array($location, $enabledLocations);

            case 'mobile-drawer':
                return in_array('mobile', $enabledLocations);

            case 'header':
                if (is_search()) {
                    return false;
                }
                return is_front_page()
                    ? in_array('header', $enabledLocations)
                    : in_array('header_sub', $enabledLocations);
            case 'mobile':
                if (is_search()) {
                    return false;
                }
                return is_front_page()
                    ? in_array('header_mobile', $enabledLocations)
                    : in_array('header_mobile_sub', $enabledLocations);

            case 'navigation':
                return !is_search() && in_array('mainmenu', $enabledLocations);

            case 'mega-menu':
                return in_array('mega_menu', $enabledLocations);

            case 'quicklinks':
                return in_array('quicklinks', $enabledLocations);

            default:
                return false;
        }
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
     * Get the appropriate home URL, considering multisite and subdirectory setups.
     *
     * @return string
     */
    protected function getHomeUrl(): string
    {
        if (is_multisite() && !is_subdomain_install() && !is_main_site()) {
            $homeUrl = $this->siteSwitcher->runInSite(
                $this->wpService->getMainSiteId(),
                function () {
                    return $this->getHomeUrl();
                }
            );
        } else {
            $homeUrl = get_home_url();
        }

        // Ensure $homeUrl is always a string
        if (!is_string($homeUrl) || $homeUrl === null) {
            $homeUrl = '';
        }

        $filtered = apply_filters('Municipio/homeUrl', esc_url($homeUrl));
        return is_string($filtered) ? $filtered : '';
    }

    /**
     * Get home url path
     * @return string
     */
    public function getHomeUrlPath(): string
    {
        $homeUrl = $this->getHomeUrl();
        $parsedUrl = wp_parse_url($homeUrl);
        return $parsedUrl['path'] ?? '/';
    }

    /**
     * Get admin url
     * @return string
     */
    protected function getAdminUrl(): string
    {
        $adminUrl = get_admin_url();
        if (!is_string($adminUrl) || $adminUrl === null) {
            $adminUrl = '';
        }
        $filtered = apply_filters('Municipio/adminUrl', $adminUrl);
        return is_string($filtered) ? $filtered : '';
    }

    /**
     * Get post published
     * @return string
     */
    protected function getPagePublished(): string
    {
        $published = get_the_time('Y-m-d');
        if (!is_string($published) || $published === null) {
            $published = '';
        }
        $filtered = apply_filters('Municipio/postPublished', $published);
        return is_string($filtered) ? $filtered : '';
    }

    /**
     * Get post modified
     * @return string
     */
    protected function getPageModified(): string
    {
        $modified = get_the_modified_time('Y-m-d');
        if (!is_string($modified) || $modified === null) {
            $modified = '';
        }
        $filtered = apply_filters('Municipio/postModified', $modified);
        return is_string($filtered) ? $filtered : '';
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
        $title = wp_title('|', false, 'right');
        if (!is_string($title) || $title === null) {
            $title = '';
        }
        $filtered = apply_filters('Municipio/postTitle', $title);
        return is_string($filtered) ? $filtered : '';
    }

    /**
     * Get language attributes
     * @return string
     */
    protected function getLanguageAttrs(): string
    {
        $attrs = apply_filters_deprecated('Municipio/language_attributes', array(get_language_attributes()), "3.0", "Municpio/languageAttributes");
        return is_string($attrs) ? $attrs : '';
    }

    /**
     * Creates a ajax url
     * @return string
     */
    protected function getAjaxUrl(): string
    {
        $url = apply_filters_deprecated('Municipio/ajax_url_in_head', array(admin_url('admin-ajax.php')), "3.0", "Municpio/ajaxUrl");
        return is_string($url) ? $url : '';
    }

    /**
     * Get body class
     * @return string
     */
    protected function getBodyClass(): string
    {
        $bodyClass = get_body_class('no-js');
        if (!is_array($bodyClass)) {
            $bodyClass = [$bodyClass];
        }
        $result = apply_filters('Municipio/bodyClass', join(' ', $bodyClass));
        return is_string($result) ? $result : '';
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
    /**
     * Convert multiline text to array, safely handling null input.
     *
     * @param string|null $text The multiline text to convert to an array.
     * @return array The array representation of the multiline text, or empty array if the text is empty/null.
     */
    public function getMultilineTextAsArray($text): array
    {
        if (!is_string($text) || empty(trim($text))) {
            return [];
        }
        return explode("\n", trim($text));
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
     * Get all public post type rss feeds
     *
     * @param array $feedTypes
     * 
     * @return array
     */
    public function getAllPublicPostTypeRssFeeds(array $feedTypes = ['rss2']): array
    {
        $feeds = [];

        $postTypes = get_post_types(
            [
                'public' => true,
            ],
            'objects'
        );

        foreach ($postTypes as $postType) {
            if (empty($postType->has_archive)) {
                continue;
            }

            if ($postType->rewrite === false) {
                continue;
            }

            if (isset($postType->feeds) && $postType->feeds === false) {
                continue;
            }

            foreach ($feedTypes as $feedType) {
                $feedUrl = get_post_type_archive_feed_link(
                    $postType->name,
                    $feedType
                );

                if ($feedUrl) {
                    $feeds[$postType->name][$feedType] = (object) [
                        'url'  => $feedUrl,
                        'name' => __('Feed') . ': ' . $postType->labels->name
                    ];
                }
            }
        }

        return $feeds;
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
