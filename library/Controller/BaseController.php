<?php

namespace Municipio\Controller;

class BaseController
{
    /**
     * Holds the view's data
     * @var array
     */
    protected $data = [];

    public function __construct()
    {
        //Html data 
        $this->data['ajaxUrl']              = $this->getAjaxUrl();
        $this->data['bodyClass']            = $this->getBodyClass();
        $this->data['languageAttributes']   = $this->getLanguageAttrs();

        //Post data 
        $this->data['postTitle']            = $this->getPostTitle(); 
        $this->data['postPublished']        = $this->getPostPublished(); 
        $this->data['postModified']         = $this->getPostModified(); 

        //Logotypes 
        $this->data['logotype']             = $this->getLogotype();

        //Navigation
        $this->data['breadcrumb']           = $this->getBreadcrumbArray(); 

        //Language
        $this->data['lang'] = array(
            'jumpToMainMenu'        => __('Jump to the main menu', 'municipio'),
            'jumpToMainContent'     => __('Jump to the main content', 'municipio'),
            'ago'                   => __("ago", 'municipio'),
            'since'                 => __("since", 'municipio'),
            'weeks'                 => __("weeks", 'municipio'),
            'days'                  => __("days", 'municipio'),
            'hours'                 => __("hours", 'municipio'),
            'minutes'               => __("minutes", 'municipio'),
            'seconds'               => __("seconds", 'municipio'),
        );

        //Google translate location
        $this->data['translateLocation'] = get_field('show_google_translate', 'option'); 

        //Admin notices (show incomplete configuration to administrator)
        if (is_user_logged_in() && current_user_can('edit_themes')) {
            $this->data['showAdminNotices'] = true;
        } else {
            $this->data['showAdminNotices'] = false;
        }

        $this->getNavigationMenus();
        $this->getHelperVariables();
        $this->getFilterData();
        $this->getVerticalMenu();
        $this->getFixedActionBar();

        $this->init();
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
     * Get post published
     * @return string
     */
    public function getPostPublished() : string
    {
        return apply_filters('Municipio/postPublished', get_the_time('Y-m-d'));
    }

    /**
     * Get post modified
     * @return string
     */
    public function getPostModified() : string
    {
        return apply_filters('Municipio/postModified', get_the_modified_time('Y-m-d'));
    }

    /**
     * Get language attributes
     * @return string
     */
    public function getBlogDescription() : string
    {
        return apply_filters('Municipio/blogDescription', get_bloginfo('description'));
    }
    
    /**
     * Get post title
     * @return string
     */
    public function getPostTitle() : string
    {
        return apply_filters('Municipio/postTitle', wp_title('|', false, 'right'));
    }

    /**
     * Get language attributes
     * @return string
     */
    public function getLanguageAttrs() : string
    {
         return apply_filters_deprecated('Municipio/language_attributes', array(get_language_attributes()), "3.0", "Municpio/languageAttributes");
    }

    /**
     * Creates a ajax url
     * @return string
     */
    public function getAjaxUrl() : string
    {
        return apply_filters_deprecated('Municipio/ajax_url_in_head', array(admin_url('admin-ajax.php')), "3.0", "Municpio/ajaxUrl");
    }

    /**
     * Get body class
     * @return string
     */
    public function getBodyClass() : string
    {
        return apply_filters('Municipio/bodyClass', join(' ', get_body_class('no-js')));
    }

    /**
     * Get breadcrumb array
     * @return array
     */
    public function getBreadcrumb() : array
    {
        return apply_filters('Municipio/breadcrumbArray', []);
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

    public function getNavigationMenus($blogId = null, $dataStoragePoint = 'navigation')
    {
        //Reset blog id if null
        if(is_null($blogId)) {
            $blogId = get_current_blog_id(); 
        }

        //Switch blog if differ blog id
        if($blogId != get_current_blog_id()) {
            switch_to_blog($blogId);
            $blogIdswitch = true;
        } else {
            $blogIdswitch = false;
        }

        $this->data[$dataStoragePoint]['headerTabsMenu'] = wp_nav_menu(array(
            'theme_location' => 'header-tabs-menu',
            'container' => 'nav',
            'container_class' => 'menu-header-tabs',
            'container_id' => '',
            'menu_class' => 'nav nav-tabs',
            'menu_id' => 'help-menu-top',
            'echo' => false,
            'before' => '',
            'after' => '',
            'link_before' => '',
            'link_after' => '',
            'items_wrap' => '<ul class="%2$s">%3$s</ul>',
            'depth' => 1,
            'fallback_cb' => '__return_false'
        ));

        $this->data[$dataStoragePoint]['headerHelpMenu'] = wp_nav_menu(array(
            'theme_location' => 'help-menu',
            'container' => 'nav',
            'container_class' => 'menu-help',
            'container_id' => '',
            'menu_class' => 'nav nav-help nav-horizontal',
            'menu_id' => 'help-menu-top',
            'echo' => false,
            'before' => '',
            'after' => '',
            'link_before' => '',
            'link_after' => '',
            'items_wrap' => '<ul class="%2$s">%3$s</ul>',
            'depth' => 1,
            'fallback_cb' => '__return_false'
        ));

        // If 404, fragment cache the navigation and return
        if (is_404()) {
            if (!wp_cache_get('404-menus', 'municipio-navigation')) {
                $navigation = new \Municipio\Helper\Navigation();
                $this->data[$dataStoragePoint]['mainMenu'] = $navigation->mainMenu();
                $this->data[$dataStoragePoint]['mobileMenu'] = $navigation->mobileMenu();

                wp_cache_add(
                    '404-menus',
                    array(
                        'mainMenu' => $this->data['navigation']['mainMenu'],
                        'mobileMenu' => $this->data['navigation']['mobileMenu']
                    ),
                    'municipio-navigation',
                    86400
                );
            } else {
                $cache = wp_cache_get('404-menus', 'municipio-navigation');
                $this->data[$dataStoragePoint]['mainMenu'] = $cache['mainMenu'];
                $this->data[$dataStoragePoint]['mobileMenu'] = $cache['mobileMenu'];
            }

        } else {
            $navigation = new \Municipio\Helper\Navigation();
            $this->data[$dataStoragePoint]['mainMenu'] = $navigation->mainMenu();
            $this->data[$dataStoragePoint]['mobileMenu'] = $navigation->mobileMenu();

            global $isSublevel;
            if ($isSublevel !== true) {
                $this->data[$dataStoragePoint]['sidebarMenu'] = $navigation->sidebarMenu();
            }
        }

        //Restore blog
        if($blogIdswitch) {
            restore_current_blog();
        }
    }

    public function getLogotype()
    {
        if (isset($this->data['logotype'])) {
            return $this->data['logotype'];
        }

        return (object) array(
            'standard' => get_field('logotype', 'option'),
            'negative' => get_field('logotype_negative', 'option')
        );
    }

    public function getHeaderLayout()
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

    public function getFooterLayout()
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

    public function getVerticalMenu()
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
        // Method body
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
