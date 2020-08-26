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
        $this->data['homeUrlPath']          = parse_url(get_home_url( ), PHP_URL_PATH);

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
        $this->data['logotype']             = $this->getLogotype();

        $breadcrumb = new \Municipio\Helper\Navigation();
        $primary = new \Municipio\Helper\Navigation();
        $secondary = new \Municipio\Helper\Navigation();
        $mobileMenu = new \Municipio\Helper\Navigation();
        $tabMenu = new \Municipio\Helper\Navigation();
        $tabMenu = new \Municipio\Helper\Navigation();
        $helpMenu = new \Municipio\Helper\Navigation();
        $dropDownMenu = new \Municipio\Helper\Navigation();

        //Breadcrumb location helper
        $this->data['breadcrumbItems']      = $breadcrumb->getBreadcrumbItems();

        //Main Navigation ($menu, $pageId = null, $fallbackToPageTree = false, $includeTopLevel = true)
        $this->data['primaryMenuItems']     = $primary->getMenuItems('main-menu', $this->getPageID(), true, true);
        $this->data['secondaryMenuItems']   = $secondary->getMenuItems('secondary-menu', $this->getPageID(), true, false);
        $this->data['mobileMenuItems']      = $mobileMenu->getMenuItems('main-menu', $this->getPageID(), true, true);

        //Complementary navigations
        $this->data['tabMenuItems']         = $tabMenu->getMenuItems('header-tabs-menu', $this->getPageID());
        $this->data['helpMenuItems']        = $helpMenu->getMenuItems('help-menu', $this->getPageID());
        $this->data['dropdownMenuItems']    = $dropDownMenu->getMenuItems('dropdown-links-menu', $this->getPageID());
        
        //Google translate location
        $this->data['translateLocation']    = get_field('show_google_translate', 'option');

        //User is authenticated
        $this->data['isAuthenticated']      = is_user_logged_in();

        //User role
        $this->data['userRole']             = $this->getUserRole();  //TODO: MOVE TO USER HELPER CLASS

        //Show admin notices
        $this->data['showAdminNotices']     = $this->showAdminNotices(); //TODO: MOVE TO USER HELPER CLASS

        //Current posttype
        $this->data['postTypeDetails']      = \Municipio\Helper\PostType::postTypeDetails();
        $this->data['postType']             = $this->data['postTypeDetails']->name; 

        //Notice storage
        $this->data['notice']               = []; 

        //Language
        $this->data['lang'] = array(
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
        );

        //Wordpress hooks
        $this->data['hook'] = (object) array(
            'loopStart' => $this->hook('loop_start'),
            'loopEnd' => $this->hook('loop_end')
        ); 

        //Structural
        $this->getHelperVariables();
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
    public function hook($hookKey) : string {
        ob_start();
        do_action($hookKey); 
        return apply_filters('Municipio/Hook/' . \Municipio\Helper\FormatObject::camelCase($hookKey), ob_get_clean());
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
        if(is_post_type_archive()) {
            $NavHelper      = new \Municipio\Helper\Navigation();
            $posttypeIds    = array_flip(
                (array) $NavHelper->getPageForPostTypeIds()
            );

            if(is_array($posttypeIds) && isset($posttypeIds[get_post_type()])) {
                return $posttypeIds[get_post_type()]; 
            }
        }

        //Get the queried page
        if(get_queried_object_id()) {
            return get_queried_object_id(); 
        }

        //Return page for frontpage (fallback)
        if($frontPageId = get_option('page_on_front')) {
           return $frontPageId;  
        }

        //Return page blog (fallback)
        if($frontPageId = get_option('page_for_posts')) {
            return $frontPageId;  
        }

        return new Exception("Whoopsie, could not find anything to build the menu from, sorry!");
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
     * Set main layout columns
     * @return void
     */
    public function layout()
    {
        $this->data['layout']['content']  = 'grid-xs-12 order-xs-1 order-md-2';
        $this->data['layout']['sidebarLeft'] = 'grid-xs-12 grid-md-4 grid-lg-3 order-xs-2 order-md-1';
        $this->data['layout']['sidebarRight'] = 'grid-xs-12 grid-md-4 grid-lg-3 hidden-xs hidden-sm hidden-md order-md-3';

        $sidebarLeft = false;
        $sidebarRight = false;

        if (get_field('archive_' . sanitize_title(get_post_type()) . '_show_sidebar_navigation', 'option') && is_post_type_archive(get_post_type())) {
            $sidebarLeft = true;
        }

        //Has child or is parent and nav_sub is enabled
        if (get_field('nav_sub_enable', 'option') && is_singular() &&
            !empty(get_children(['post_parent' => get_queried_object_id(), 'numberposts' => 1], ARRAY_A))
            || get_field('nav_sub_enable', 'option') && is_singular() &&
            count(get_children(['post_parent' => get_queried_object_id(), 'numberposts' => 1], ARRAY_A)) > 0) {
            $sidebarLeft = true;
        }

        if (is_active_sidebar('left-sidebar') || is_active_sidebar('left-sidebar-bottom')) {
            $sidebarLeft = true;
        }

        if (is_active_sidebar('right-sidebar')) {
            $sidebarRight = true;
        }

        if ($sidebarLeft && $sidebarRight) {
            $this->data['layout']['content']  = 'grid-xs-12 grid-md-8 grid-lg-6 order-xs-1 order-md-2';
        } elseif ($sidebarLeft || $sidebarRight) {
            $this->data['layout']['content']  = 'grid-xs-12 grid-md-8 grid-lg-9 order-xs-1 order-md-2';
        }

        if (!$sidebarLeft && $sidebarRight) {
            $this->data['layout']['sidebarLeft'] .= ' hidden-lg';
        }

        if (is_front_page()) {
            $this->data['layout']['content']  = 'grid-xs-12';
        }

        $this->data['layout'] = apply_filters('Municipio/Controller/BaseController/Layout', $this->data['layout'], $sidebarLeft, $sidebarRight);
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

    public function getFixedActionBar()
    {
        $this->data['fab'] = \Municipio\Theme\FixedActionBar::getFab();
    }

    public function getFilterData()
    {
        $this->data = array_merge(
            $this->data,
            apply_filters_deprecated('Municipio/controller/base/view_data', array($this->data), "2.0", 'Municipio/viewData')
        );
    }

    public function getHelperVariables()
    {

        // TODO: Remove left sidebar, rename right to "has sidebar"

        $this->data['hasRightSidebar'] = get_field('right_sidebar_always', 'option') || is_active_sidebar('right-sidebar');
        $this->data['hasLeftSidebar'] = (isset($this->data['navigation']['sidebarMenu']) && strlen($this->data['navigation']['sidebarMenu']) > 0) || is_active_sidebar('left-sidebar') || is_active_sidebar('left-sidebar-bottom');

        $contentGridSize = 'grid-xs-12';

        if ($this->data['hasLeftSidebar'] && $this->data['hasRightSidebar']) {
            $contentGridSize = 'grid-md-8 grid-lg-6';
        } elseif (!$this->data['hasLeftSidebar'] && $this->data['hasRightSidebar']) {
            $contentGridSize = 'grid-md-8 grid-lg-9';
        } elseif ($this->data['hasLeftSidebar'] && !$this->data['hasRightSidebar']) {
            $contentGridSize = 'grid-md-8 grid-lg-9';
        }

        $this->data['contentGridSize'] = $contentGridSize;
    }

    public function getLogotype()
    {
        //Cache, early bailout
        if (isset($this->data['logotype']) && empty($this->data['logotype'])) {
            return $this->data['logotype'];
        }

        //Get fresh logotypes
        return (object) array(
            'standard' => array_merge(['url' => ""], (array) get_field('logotype', 'option')),
            'negative' => array_merge(['url' => ""], (array) get_field('logotype_negative', 'option'))
        );
    }

    public function getHeaderLayout() //TODO: Do we need this? 
    {
        $headerLayoutSetting = get_field('header_layout', 'option');

        $classes = array();
        $classes[] = 'site-header';
        $classes[] = 'header-' . $headerLayoutSetting;

        if (is_front_page() && get_field('header_transparent', 'option')) {
            $classes[] = 'header-transparent';
        }

        if (get_field('header_centered', 'option')) {
            $classes[] = 'header-center';
        }

        switch (get_field('header_content_color', 'option')) {
            case 'light':
                $classes[] = 'header-light';
                break;

            case 'dark':
                $classes[] = 'header-dark';
                break;
        }

        $this->data['headerLayout'] = array(
            'classes' => implode(' ', $classes),
            'template' => 'default'
        );

        if (!empty($headerLayoutSetting) && !in_array($headerLayoutSetting, array('business', 'casual', 'contrasted-nav'))) {
            $this->data['headerLayout']['template'] = $headerLayoutSetting;
        }
    }

    public function getFooterLayout() //TODO: Do we need this?
    {
        $headerLayoutSetting = (get_field('footer_layout', 'option')) ? get_field('footer_layout', 'option') : 'default';

        $classes = array();
        $classes[] = 'main-footer';
        $classes[] = 'hidden-print';
        $classes[] = (get_field('scroll_elevator_enabled', 'option')) ? 'scroll-elevator-toggle' : '';
        $classes[] = 'header-' . $headerLayoutSetting;

        $this->data['footerLayout'] = array(
            'classes' => implode(' ', $classes),
            'template' => 'default'
        );

        if (!empty($footerLayoutSettings)) {
            $this->data['footerLayout']['template'] = $headerLayoutSetting;
        }
    }

    public function getVerticalMenu() //TODO: Do we need this?
    {
        //Define
        $abortFunction = true;

        //Check if these sidebars is active before running
        $triggerBySidebar = apply_filters('Municipio/Menu/Vertical/EnabledSidebars', array('top-sidebar', 'bottom-sidebar'));
        foreach ((array) $triggerBySidebar as $sidebar) {
            if (is_active_sidebar($sidebar)) {
                $abortFunction = false;
            }
        }

        //No active sidebars, abort
        if ($abortFunction === true) {
            return false;
        }

        //Return items to view. Format: array(array('title' => '', 'link' => ''))
        $this->data['verticalNav'] = apply_filters('Municipio/Menu/Vertical/Items', array());

        return true;
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
        if (is_null($local)) {
            $this->$global = $$global;
        } else {
            $this->$local = $$global;
        }
    }
}
