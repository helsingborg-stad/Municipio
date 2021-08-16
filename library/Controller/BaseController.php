<?php

namespace Municipio\Controller;

class BaseController
{
    /**
     * Holds the view's data
     * @var array
     */
    protected $data = [];

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
     * Init data fetching
     * @var object
     */
    public function __construct()
    {

        //Store globals
        $this->globalToLocal('wp_query', 'wpQuery');
        $this->globalToLocal('posts');

        //Send globals to view
        $this->data['wpQuery']              = $this->wpQuery;
 
        //Header & Footer
        $this->data['wpHeader']             = $this->getWpHeader();
        $this->data['wpFooter']             = $this->getWpFooter();

        //Basic
        $this->data['ajaxUrl']              = $this->getAjaxUrl();
        $this->data['bodyClass']            = $this->getBodyClass();
        $this->data['languageAttributes']   = $this->getLanguageAttrs();
        $this->data['homeUrl']              = $this->getHomeUrl();
        $this->data['adminUrl']             = $this->getAdminUrl();
        $this->data['homeUrlPath']          = parse_url(get_home_url(), PHP_URL_PATH);
        $this->data['siteName']             = $this->getSiteName();

        //View porperties
        $this->data['isFrontPage']          = is_front_page();
        $this->data['isSingular']           = is_singular();
        $this->data['isSingle']             = is_single();
        $this->data['isSticky']             = is_sticky();

        //Post data
        $this->data['pageTitle']            = $this->getPageTitle();
        $this->data['pagePublished']        = $this->getPagePublished();
        $this->data['pageModified']         = $this->getPageModified();
        $this->data['pageID']               = $this->getPageID();
        $this->data['pageParentID']         = $this->getPageParentID();

        //Logotypes
        $this->data['logotype']             = $this->getLogotype(get_field('header_logotype', 'option'));
        $this->data['footerLogotype']       = $this->getLogotype(get_field('footer_logotype', 'option'));
        $this->data['emblem']               = $this->getEmblem();

        //Get header layout
        $this->data['headerLayout'] = get_field('header_layout', 'option') ?? 'business';

        //Init class for menus
        $breadcrumb     = new \Municipio\Helper\Navigation('breadcrumb');
        $primary        = new \Municipio\Helper\Navigation('primary');
        $secondary      = new \Municipio\Helper\Navigation('sidebar');
        $mobileMenu     = new \Municipio\Helper\Navigation('mobile');
        $tabMenu        = new \Municipio\Helper\Navigation('tab');
        $helpMenu       = new \Municipio\Helper\Navigation('help');
        $dropDownMenu   = new \Municipio\Helper\Navigation('dropdown');
        $floatingMenu   = new \Municipio\Helper\Navigation('floating');

        //Breadcrumb location helper
        $this->data['breadcrumbItems']      = $breadcrumb->getBreadcrumbItems($this->getPageID());
        
        //Main Navigation ($menu, $pageId = null, $fallbackToPageTree = false, $includeTopLevel = true, $onlyKeepFirstLevel = false)
        $this->data['primaryMenuItems']     = $primary->getMenuItems('main-menu', $this->getPageID(), true, true, true);
        $this->data['secondaryMenuItems']   = $secondary->getMenuItems('secondary-menu', $this->getPageID(), false, true);
        $this->data['singleMenuItems']      = $secondary->getMenuItems(false, $this->getPageID(), true, false);
        $this->data['mobileMenuItems']      = $mobileMenu->getMenuItems(false, $this->getPageID(), true, true);

        //Complementary navigations
        $this->data['tabMenuItems']         = $tabMenu->getMenuItems('header-tabs-menu', $this->getPageID());
        $this->data['helpMenuItems']        = $helpMenu->getMenuItems('help-menu', $this->getPageID());
        $this->data['dropdownMenuItems']    = $dropDownMenu->getMenuItems('dropdown-links-menu', $this->getPageID());
        $this->data['floatingMenuItems']    = $floatingMenu->getMenuItems('floating-menu', $this->getPageID(), false, true, true);

        //Get labels for menu
        $this->data['floatingMenuLabels']   = $this->getFloatingMenuLabels(); 

        // Show sidebars if not set to false in template controllers
        $this->data['showSidebars']         = true;

        //User is authenticated
        $this->data['isAuthenticated']      = is_user_logged_in();

        //User role
        $this->data['userRole']             = $this->getUserRole();  //TODO: MOVE TO USER HELPER CLASS

        //Show admin notices
        $this->data['showAdminNotices']     = $this->showAdminNotices(); //TODO: MOVE TO USER HELPER CLASS

        //Search
        $this->data['showHeaderSearch']     = $this->showSearchForm('header');
        $this->data['showNavigationSearch'] = $this->showSearchForm('navigation'); 
        $this->data['showHeroSearch']       = $this->showSearchForm('hero'); 
        $this->data['showMobileSearch']     = $this->showSearchForm('mobile');
        $this->data['searchQuery']          = get_search_query(); 

        //Current posttype
        $this->data['postTypeDetails']      = \Municipio\Helper\PostType::postTypeDetails();
        $this->data['postType']             = $this->data['postTypeDetails']->name;

        //Notice storage
        $this->data['notice']               = [];

        //Secondary nav positions
        $this->data['secondaryNavPostion']  = $this->getNavPosition('secondary'); 

        //Mobile Menu Drawer Style
        $this->data['mobileMenuDrawerStyle'] = $this->getMobileDrawerStyle();

        //Column sizes
        $this->data['leftColumnSize']  = $this->getColumnSize('left'); 
        $this->data['rightColumnSize']  = $this->getColumnSize('right'); 

        //Language
        $this->data['lang'] = (object) array(
            'goToHomepage'          => __("Go to homepage", 'municipio'),
            'jumpToMainMenu'        => __("Jump to the main menu", 'municipio'),
            'jumpToMainContent'     => __("Jump to the main content", 'municipio'),
            'ago'                   => __("ago", 'municipio'),
            'since'                 => __("since", 'municipio'),
            'weeks'                 => __("weeks", 'municipio'),
            'days'                  => __("days", 'municipio'),
            'hours'                 => __("hours", 'municipio'),
            'minutes'               => __("minutes", 'municipio'),
            'seconds'               => __("seconds", 'municipio'),
            'search'                => __("Search", 'municipio'),
            'searchOn'              => __("Search on", 'municipio'),
            'searchQuestion'        => __("What are you searching for?", 'municipio'),
            'primaryNavigation'     => __("Primary navigation", 'municipio'),
            'relatedLinks'          => __("Related links", 'municipio'),
            'menu'                  => __("Menu", 'municipio'),
            'emblem'                => __("Site emblem", 'municipio'),
        );

        //Wordpress hooks
        $this->data['hook'] = (object) array(
            'innerLoopStart' => $this->hook('inner_loop_start'),
            'innerLoopEnd' => $this->hook('inner_loop_end'),
            'loopStart' => $this->hook('loop_start'),
            'loopEnd' => $this->hook('loop_end')
        );

        //Structural
        $this->getFilterData();

        $this->init();
    }

    /**
     * Run WordPress hooks
     *
     * @param string $hookKey
     *
     * @return mixed Returns the output of the hook, mixed values.
     */
    public function hook($hookKey) : string
    {
        ob_start();
        do_action($hookKey);
        return apply_filters('Municipio/Hook/' . \Municipio\Helper\FormatObject::camelCase($hookKey), ob_get_clean());
    }

    /**
     * Get WordPress header
     *
     * @return string
     */
    public function getSiteName() : string
    {
        return apply_filters('Municipio/SiteName', get_bloginfo('name'));
    }

    /**
     * Get WordPress header
     *
     * @return string
     */
    public function getWpHeader() : string
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
    public function getWpFooter() :  string
    {
        ob_start();
        wp_footer();
        return apply_filters('Municipio/FooterHTML', ob_get_clean());
    }

    /**
     * Get current page ID
     */
    public function getPageID() : int
    {

        //Page for posttype archive mapping result
        if (is_post_type_archive()) {
            if ($pageId = get_option('page_for_' . get_post_type())) {
                return $pageId;
            }
        }

        //Get the queried page
        if (get_queried_object_id()) {
            return get_queried_object_id();
        }

        //Return page for frontpage (fallback)
        if ($frontPageId = get_option('page_on_front')) {
            return $frontPageId;
        }

        //Return page blog (fallback)
        if ($frontPageId = get_option('page_for_posts')) {
            return $frontPageId;
        }

        return 0;
    }

    /**
     * Get current parent ID
     *
     * @return integer
     */
    public function getPageParentID() : int
    {
        return wp_get_post_parent_id($this->getPageID());
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
     * Get floating menu labels
     *
     * @return object
     */
    public function getFloatingMenuLabels() : object
    {
        $menuObject = wp_get_nav_menu_object(get_nav_menu_locations()['floating-menu']); 

        return (object) apply_filters('Municipio/FloatingMenuLabels', 
            [
                'heading' => get_field('floating_popup_heading', $menuObject), 
                'buttonLabel' => get_field('floating_toggle_button_label', $menuObject),
                'buttonIcon' => get_field('toggle_button_icon', $menuObject)
            ]
        );
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
     * Determine if search boxes should be displayed
     *
     * @param string $location
     * @return boolean
     */
    protected function showSearchForm($location = null) {

        $enabledLocations = (array) get_field('search_display', 'option'); 

        if($location == "hero") {
            return in_array($location, $enabledLocations); 
        }

        if($location == "mobile") {

            //Do not show on frontpage, if hero search is active
            if(!in_array("hero", $enabledLocations) && is_front_page()) {
                return true; 
            }

            //Show if not frontpage, not search and search is enabled anywhere else. 
            if(!is_front_page() && !is_search() && !empty($enabledLocations)) {
                return true; 
            }
        }

        if($location == "header") {

            if(is_search()) {
                return false; 
            }

            if(is_front_page()) {
                return in_array('header', $enabledLocations);
            }

            if(!is_front_page()) {
                return in_array('header_sub', $enabledLocations);
            }
        }
        
        if($location == "navigation") {

            if(is_search()) {
                return false; 
            }

            return in_array('mainmenu', $enabledLocations); 
        }

        return false; 
    }

    /**
     * Get getPostTypeDetails
     * @return string
     */
    protected function getPostTypeDetails() : object
    {
        return apply_filters('Municipio/postTypeDetails', (object) get_post_type_object(get_post_type()));
    }

    /**
     * Get home url
     * @return string
     */
    protected function getHomeUrl() : string
    {
        return apply_filters('Municipio/homeUrl', esc_url(get_home_url()));
    }

    /**
     * Get admin url
     * @return string
     */
    protected function getAdminUrl() : string
    {
        return apply_filters('Municipio/adminUrl', get_admin_url());
    }

    /**
     * Get post published
     * @return string
     */
    protected function getPagePublished() : string
    {
        return apply_filters('Municipio/postPublished', get_the_time('Y-m-d'));
    }

    /**
     * Get post modified
     * @return string
     */
    protected function getPageModified() : string
    {
        return apply_filters('Municipio/postModified', get_the_modified_time('Y-m-d'));
    }

    /**
     * Get language attributes
     * @return string
     */
    protected function getBlogDescription() : string
    {
        return apply_filters('Municipio/blogDescription', get_bloginfo('description'));
    }
    
    /**
     * Get post title
     * @return string
     */
    protected function getPageTitle() : string
    {
        return apply_filters('Municipio/postTitle', wp_title('|', false, 'right'));
    }

    /**
     * Get language attributes
     * @return string
     */
    protected function getLanguageAttrs() : string
    {
        return apply_filters_deprecated('Municipio/language_attributes', array(get_language_attributes()), "3.0", "Municpio/languageAttributes");
    }

    /**
     * Creates a ajax url
     * @return string
     */
    protected function getAjaxUrl() : string
    {
        return apply_filters_deprecated('Municipio/ajax_url_in_head', array(admin_url('admin-ajax.php')), "3.0", "Municpio/ajaxUrl");
    }

    /**
     * Get body class
     * @return string
     */
    protected function getBodyClass() : string
    {
        return apply_filters('Municipio/bodyClass', join(' ', get_body_class('no-js')));
    }

    /**
     * Get breadcrumb array
     * @return array
     */
    protected function getBreadcrumb() : array
    {
        return apply_filters('Municipio/breadcrumbArray', breadcrumb());
    }

    public function getFilterData()
    {
        $this->data = array_merge(
            $this->data,
            apply_filters_deprecated('Municipio/controller/base/view_data', array($this->data), "2.0", 'Municipio/viewData'),
            apply_filters('Municipio/viewData', $this->data)
        );
    }

    /**
     * Get position of navigation display
     *
     * @return null|string
     */
    public function getNavPosition($identifier) {

        $mods = get_theme_mods(); 

        //Secondary navigation
        if($identifier == 'secondary') {
            if(isset($mods['site']) && isset($mods['site']['field_60cb4dd897cb8'])) {
                if(in_array($mods['site']['field_60cb4dd897cb8'], ['left', 'right', 'hidden'])) {
                    return $mods['site']['field_60cb4dd897cb8']; 
                }
            }
            return 'left'; 
        }
        
        return null; 
    }

    /**
     * Get size of column
     *
     * @return integer
     */
    public function getColumnSize($identifier) {

        $mods = get_theme_mods(); 

        //Secondary navigation
        if($identifier == 'left') {
            $fieldId = 'field_60d339b60049e'; 
        }

        if($identifier == 'right') {
            $fieldId = 'field_60d3393d1231a'; 
        }

        if(isset($mods['site']) && isset($mods['site'][$fieldId])) {
            if(in_array($mods['site'][$fieldId], ['large'])) {
                return 4; 
            }
        }

        return 3; 
    }

    /**
     * Get emblem svg
     *
     * @return bool|string
     */
    public function getEmblem() {
        return get_field('logotype_emblem', 'option') ?? false; 
    }

    /**
     * Get the logotype
     *
     * @param string $variant
     * @return object
     */
    public function getLogotype($variant = "standard")
    {
        //Cache, early bailout
        if (isset($this->data['logotype']) && empty($this->data['logotype'])) {
            return $this->data['logotype'];
        }

        //Get fresh logotypes
        $variantKey = "logotype";

        //Builds acf-field name
        if ($variant !== "standard") {
            $variantKey = $variantKey . '_' . $variant;
        }

        //Get the logo, enshure url is defined. 
        $logotype = array_merge(['url' => ""], (array) get_field($variantKey, 'option'));

        //Fallback to municipio logo, if undefined. 
        if(empty(array_filter($logotype)) && $variantKey == "logotype") {
            $logotype = ['url' => get_stylesheet_directory_uri() . '/assets/images/municipio.svg']; 
        }

        //Return
        return (object) $logotype; 
    }


    /**
     * Retrieve and return style for mobile menu drawer
     * @return void
     */
    public function getMobileDrawerStyle() {
        $mod = get_theme_mods();
        
        return $mod['site']['field_61126702da36c'];
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

        if(is_null($$global)) {
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
