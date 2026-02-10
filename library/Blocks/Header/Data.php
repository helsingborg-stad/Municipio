<?php

namespace Municipio\Blocks\Header;

use AcfService\AcfService;
use Municipio\Controller\Navigation\Config\MenuConfig;
use Municipio\Controller\Navigation\MenuBuilderInterface;
use Municipio\Controller\Navigation\MenuDirector;
use Municipio\Helper\FormatObject;
use Municipio\Helper\SiteSwitcher\SiteSwitcherInterface;
use Municipio\Helper\TranslatedLabels;
use Municipio\Helper\User\User;
use WpService\WpService;

class Data
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
        private MenuBuilderInterface $menuBuilder,
        private MenuDirector $menuDirector,
        private WpService $wpService,
        private SiteSwitcherInterface $siteSwitcher,
        private User $userHelper,
    ) {
        //Store globals
        // $this->globalToLocal('wp_query', 'wpQuery');
        // $this->globalToLocal('posts');
        // $this->globalToLocal('wpdb', 'db');

        //Basic
        // $this->data['ajaxUrl'] = $this->getAjaxUrl();
        // $this->data['bodyClass'] = $this->getBodyClass();
        // $this->data['languageAttributes'] = $this->getLanguageAttrs();
        $this->data['homeUrl'] = $this->getHomeUrl();
        // $this->data['adminUrl'] = $this->getAdminUrl();
        //$this->data['homeUrlPath'] = $this->getHomeUrlPath();
        //$this->data['siteName'] = $this->getSiteName();

        // Feeds
        // $this->data['rssFeedUrls'] = $this->getAllPublicPostTypeRssFeeds();

        //Post data
        // $this->data['pageID'] = CurrentPostId::get();
        // $this->data['pageParentID'] = $this->getPageParentID();

        //Customization data
        $this->data['customizer'] = apply_filters('Municipio/Controller/Customizer', []);

        //Logotypes
        $this->data['logotype'] = $this->getLogotype($this->data['customizer']->headerLogotype ?? 'standard', true);
        // $this->data['footerLogotype'] = $this->getLogotype($this->data['customizer']->footerLogotype ?? 'negative');
        // $this->data['subfooterLogotype'] = $this->getSubfooterLogotype($this->data['customizer']->footerSubfooterLogotype ?? false);
        // $this->data['emblem'] = $this->getEmblem();
        // $this->data['showEmblemInHero'] = $this->data['customizer']->showEmblemInHero ?? true;
        $brandTextOption = get_option('brand_text', '');
        $this->data['brandText'] = $this->getMultilineTextAsArray(is_string($brandTextOption) ? $brandTextOption : '');
        $this->data['headerBrandEnabled'] = $this->data['customizer']?->headerBrandEnabled && !empty($this->data['brandText']);

        // Footer

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
        $mobileMenuConfig = new MenuConfig(
            'mobile',
            'secondary-menu',
            false,
            false,
            !empty($this->data['customizer']->mobileMenuPagetreeFallback),
        );

        $this->menuBuilder->setConfig($mobileMenuConfig);
        $mobileMenuConfig->getFallbackToPageTree() ? $this->menuDirector->buildMixedPageTreeMenu(true) : $this->menuDirector->buildStandardMenu();
        $this->data['mobileMenu'] = $this->menuBuilder->getMenu()->getMenu();

        // Primary menu
        $primaryMenuConfig = new MenuConfig(
            'primary',
            'main-menu',
            isset($this->data['customizer']->primaryMenuDropdown) ? !$this->data['customizer']->primaryMenuDropdown : false,
            false,
            !empty($this->data['customizer']->primaryMenuPagetreeFallback),
        );

        $this->menuBuilder->setConfig($primaryMenuConfig);
        $primaryMenuConfig->getFallbackToPageTree() ? $this->menuDirector->buildStandardWithPageTreeFallbackMenu() : $this->menuDirector->buildStandardMenu();
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
            !empty($this->data['customizer']->megaMenuPagetreeFallback),
        );

        $this->menuBuilder->setConfig($megaMenuConfig);
        $megaMenuConfig->getFallbackToPageTree() ? $this->menuDirector->buildMixedPageTreeMenu() : $this->menuDirector->buildStandardMenu();
        $this->data['megaMenu'] = $this->menuBuilder->getMenu()->getMenu();

        $quicklinksMenuConfig = new MenuConfig(
            'quicklinks',
            'quicklinks-menu',
            true,
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
        $this->data['helpMenuItems'] = $this->menuBuilder->getMenu()->getMenu()['items'] ?? [];

        // Dropdown menu
        // TODO: Find out what it does
        $dropdownMenuConfig = new MenuConfig(
            'dropdown',
            'dropdown-links-menu',
        );

        $this->menuBuilder->setConfig($dropdownMenuConfig);
        $this->menuDirector->buildStandardMenu();
        $this->data['dropdownMenuItems'] = $this->menuBuilder->getMenu()->getMenu()['items'] ?? [];

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
            !empty($this->data['customizer']->secondaryMenuPagetreeFallback),
        );

        $this->menuBuilder->setConfig($secondaryMenuPostTypeConfig);
        $this->menuDirector->buildStandardMenu();
        $secondaryMenu = $this->menuBuilder->getMenu()->getMenu();

        if (empty($secondaryMenu['items'])) {
            $this->menuBuilder->setConfig($secondaryMenuConfig);
            $secondaryMenuConfig->getFallbackToPageTree() ? $this->menuDirector->buildMixedPageTreeMenu() : $this->menuDirector->buildStandardMenu();
            $secondaryMenu = $this->menuBuilder->getMenu()->getMenu();
        }

        $this->data['secondaryMenu'] = $secondaryMenu;

        //Get labels for menu
        $this->data['floatingMenuLabels'] = $this->getFloatingMenuLabels();
        //$this->data['quicklinksOptions'] = $this->getQuicklinksOptions();

        //Get language menu options
        $this->data['languageMenuOptions'] = $this->getLanguageMenuOptions();

        //User is authenticated
        $this->data['user'] = $this->wpService->wpGetCurrentUser();
        $this->data['isAuthenticated'] = $this->wpService->isUserLoggedIn();
        $this->data['isAdminBarShowing'] = $this->wpService->isAdminBarShowing();
        $this->data['loginUrl'] = $this->wpService->wpLoginUrl(
            $this->getCurrentUrl(['loggedin' => 'true']),
        );
        $this->data['logoutUrl'] = $this->wpService->wpLogoutUrl(
            $this->getCurrentUrl(['loggedout' => 'true']),
        );

        // User basic details
        $this->data['userDetails'] = $this->getUserDetails();

        //User role
        $this->data['userRole'] = $this->getUserRole(); //TODO: MOVE TO USER HELPER CLASS

        //User group
        $this->data['userGroup'] = $this->wpService->isUserLoggedIn()
            ? (object) [
                'group' => $this->userHelper->getUserGroup(),
                'url' => $this->userHelper->getUserGroupUrl(),
                'shortname' => $this->userHelper->getUserGroupShortname(),
            ]
            : null;

        //Show admin notices
        $this->data['showAdminNotices'] = $this->showAdminNotices(); //TODO: MOVE TO USER HELPER CLASS

        //Search
        $this->data['showHeaderSearchDesktop'] = $this->showSearchForm('header');
        $this->data['showHeaderSearchMobile'] = $this->showSearchForm('mobile');
        $this->data['showNavigationSearch'] = $this->showSearchForm('navigation');
        $this->data['showQuicklinksSearch'] = $this->showSearchForm('quicklinks');
        $this->data['showMegaMenuSearch'] = $this->showSearchForm('mega-menu');
        $this->data['showHeroSearch'] = $this->showSearchForm('hero');
        $this->data['showMobileSearchDrawer'] = $this->showSearchForm('mobile-drawer');
        $this->data['searchQuery'] = get_search_query();

        // Current posttype
        $this->data['postTypeDetails'] = \Municipio\Helper\PostType::postTypeDetails();
        $this->data['postType'] = $this->data['postTypeDetails']->name ?? '';

        // Get page template
        $this->data['pageTemplate'] = $this->getPageTemplate();

        // Skip links
        $this->data['skipToMainContentLink'] = $this->setSkipLinkValue();
        $this->data['hasSideMenu'] = $this->hasSideMenu();
        $this->data['hasMainMenu'] = $this->hasMainMenu();

        $this->data['structuredData'] = \Municipio\Helper\Data::normalizeStructuredData([]);

        //Notice storage
        $this->data['notice'] = [];

        //Language
        $this->data['lang'] = TranslatedLabels::getLang(
            [
                'searchFor' => ucfirst(strtolower($this->data['postTypeDetails']->labels->search_items ?? __('Search for content', 'municipio'))),
                'noResult' => $this->data['postTypeDetails']->labels->not_found ?? __('No items found at this query.', 'municipio'),
                'logout' => __('Logout', 'municipio'),
                'login' => __('Login', 'municipio'),
                'close' => __('Close', 'municipio'),
            ],
        );

        $this->data['labels'] = (array) $this->data['lang'];

        add_filter(
            'ComponentLibrary/Component/Lang',
            function ($obj) {
                $lang = [
                    'visit' => __('Visit', 'municipio'),
                    'email' => __('Email', 'municipio'),
                    'call' => __('Call', 'municipio'),
                    'address' => __('Address', 'municipio'),
                    'visitingAddress' => __('Visiting address', 'municipio'),
                ];

                return (object) array_merge((array) $obj, $lang);
            },
            10,
            1,
        );

        if (!empty($_SERVER['HTTP_HOST'])) {
            add_filter(
                'ComponentLibrary/Component/Attribute',
                function ($attributes) {
                    if (!empty($attributes['href'])) {
                        $parsedUrl = parse_url($attributes['href']);

                        if (!empty($parsedUrl['host']) && $parsedUrl['host'] !== (empty($_SERVER['HTTP_HOST']) ? '' : $_SERVER['HTTP_HOST'])) {
                            $attributes['data-js-original-link'] = $attributes['href'];
                        }
                    }

                    return $attributes;
                },
                10,
                1,
            );
        }
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
            'id' => $user->ID,
            'email' => $user->user_email,
            'displayname' => $user->display_name,
            'firstname' => $user->first_name,
            'lastname' => $user->last_name,
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
        return urldecode($permalink ?? '');
    }

    /**
     * Should show admin notices
     */
    private function showAdminNotices()
    {
        if (is_user_logged_in() && current_user_can('edit_themes')) {
            return true;
        }
        return false;
    }

    /**
     * Get floating menu labels
     *
     * @return object
     */
    private function getFloatingMenuLabels(): object
    {
        $menuObject = wp_get_nav_menu_object(get_nav_menu_locations()['floating-menu'] ?? '');

        return (object) apply_filters(
            'Municipio/FloatingMenuLabels',
            [
                'heading' => get_field('floating_popup_heading', $menuObject),
                'buttonLabel' => get_field('floating_toggle_button_label', $menuObject),
                'buttonIcon' => get_field('toggle_button_icon', $menuObject),
            ],
        );
    }

    /**
     * Get language menu options
     *
     * @return object
     */
    private function getLanguageMenuOptions(): object
    {
        $options = wp_get_nav_menu_object(get_nav_menu_locations()['language-menu'] ?? '');

        $options = [
            'disclaimer' => get_field('language_menu_disclaimer', $options),
            'moreLanguageLink' => get_field('language_menu_more_languages', $options),
            'displayCurrentLanguage' => get_field('display_current_language', $options) ?? false,
            'currentLanguage' => null,
        ];

        // IF displayCurrentLanguage is set to true, we will get the current language
        if ($options['displayCurrentLanguage'] === true) {
            $locale = \get_locale();
            $getCurrentLang = fn() => class_exists('Locale') ? (function_exists('mb_ucfirst') ? mb_ucfirst(\Locale::getDisplayLanguage($locale, $locale)) : ucfirst(\Locale::getDisplayLanguage($locale, $locale))) : $locale;
            $options['currentLanguage'] = $getCurrentLang();
        }

        return (object) $options;
    }

    /**
     * Get current user role
     * @return mixed    String or false with role
     */
    private function getUserRole()
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
    private function getPageTemplate()
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
    private function setSkipLinkValue()
    {
        if ($this->data['pageTemplate'] === 'one-page.blade.php') {
            return apply_filters('Municipio/Controller/SkipToMainContentLinkOnePage', '#main-content');
        }
        return apply_filters('Municipio/Controller/SkipToMainContentLinkDefaultValue', '#article');
    }

    /**
     * Check if page has side menu
     */
    private function hasSideMenu()
    {
        if (!empty($this->data['secondaryMenu']['items']) && $this->data['pageTemplate'] !== 'one-page.blade.php') {
            return true;
        }
        return false;
    }

    /**
     * Check if page has main menu
     */
    private function hasMainMenu()
    {
        if (!empty($this->data['primaryMenu']['items'])) {
            return true;
        }
        return false;
    }

    /**
     * Determine if search boxes should be displayed
     *
     * @param string $location
     * @return boolean
     */
    private function showSearchForm($location = null)
    {
        $customizer = $this->data['customizer'] ?? null;
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
                return is_front_page() ? in_array('header', $enabledLocations) : in_array('header_sub', $enabledLocations);
            case 'mobile':
                if (is_search()) {
                    return false;
                }
                return is_front_page() ? in_array('header_mobile', $enabledLocations) : in_array('header_mobile_sub', $enabledLocations);

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
     * Get the appropriate home URL, considering multisite and subdirectory setups.
     *
     * @return string
     */
    private function getHomeUrl(): string
    {
        if (is_multisite() && !is_subdomain_install() && !is_main_site()) {
            $homeUrl = $this->siteSwitcher->runInSite(
                $this->wpService->getMainSiteId(),
                function () {
                    return $this->getHomeUrl();
                },
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
     * Get emblem svg
     *
     * @return bool|string
     */
    private function getEmblem()
    {
        if (empty($logotypeEmblem = $this->data['customizer']?->logotypeEmblem)) {
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
    private function getLogotype($variant = 'standard', $fallback = false): string
    {
        $variantKey = 'logotype';

        if ($variant !== 'standard' && !is_null($variant)) {
            $variantKey = FormatObject::camelCaseString("{$variantKey}_{$variant}");
        }

        $logotypeUrl = isset($this->data['customizer']->$variantKey) ? $this->data['customizer']->{$variantKey} : '';

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
    private function getDefaultLogotype(): string
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
    private function getMultilineTextAsArray($text): array
    {
        if (!is_string($text) || empty(trim($text))) {
            return [];
        }
        return explode("\n", trim($text));
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
        $this->data = apply_filters_deprecated('HbgBlade/data', array($this->data), '2.0', 'Municipio/viewData');

        //General filter
        return $this->data;
    }
}
