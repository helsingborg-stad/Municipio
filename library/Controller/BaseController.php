<?php

namespace Municipio\Controller;

class BaseController
{
    /**
     * Holds the view's data
     * @var array
     */
    protected $data = array();

    public function __construct()
    {

        /* Preview mode 2.0 */
        if (apply_filters('Municipio/Controller/BaseController/Customizer', false)) {
            $this->customizerHeader();
            $this->customizerFooter();
        } else {
            $this->getLogotype();
            $this->getHeaderLayout();
        }

        //Main
        $this->getGeneral();
        $this->getAjaxUrl();
        $this->getBodyClass();
        $this->getLanguageAttributes();

        //Language
        $this->data['lang'] = array(
            'jumpToMainMenu' => __('Jump to the main menu', 'municipio'),
            'jumpToMainContent' => __('Jump to the main content', 'municipio'),
            'ago'   => __("ago", 'municipio'),
            'since'   => __("since", 'municipio'),
            'weeks'   => __("weeks", 'municipio'),
            'days'   => __("days", 'municipio'),
            'hours'   => __("hours", 'municipio'),
            'minutes'   => __("minutes", 'municipio'),
            'seconds'   => __("seconds", 'municipio'),
        );

        //Admin notices (show incomplete configuration to administrator)
        if (is_user_logged_in() && current_user_can('edit_themes')) {
            $this->data['showAdminNotices'] = true;
        } else {
            $this->data['showAdminNotices'] = false;
        }

        $this->getFooterLayout();
        $this->getNavigationMenus();
        $this->getHelperVariables();
        $this->getFilterData();
        $this->getVerticalMenu();
        $this->getFixedActionBar();

        $this->init();
    }

    /**
     * General site data (meta tags)
     * @return void
     */
    public function getGeneral()
    {
        //General blog details / title
        $this->data['wpTitle'] = wp_title('|', false, 'right') . get_bloginfo('name');
        $this->data['description']  = get_bloginfo('description');

        //Timestamps for post
        $this->data['published'] = get_the_time('Y-m-d');
        $this->data['modified'] = get_the_modified_time('Y-m-d');
    }

    /**
     * Get language attributes
     * @return void
     */
    public function getLanguageAttributes()
    {
        $this->data['languageAttributes'] = get_language_attributes();
    }

    /**
     * Creates a ajax url
     * @return void
     */
    public function getAjaxUrl()
    {
        $this->data['ajaxUrl'] = apply_filters_deprecated('Municipio/ajax_url_in_head', array(admin_url('admin-ajax.php')), "2.0", "Municpio/ajaxUrl");
    }

    /**
     * Get body class
     * @return void
     */
    public function getBodyClass()
    {
        $this->data['bodyClass'] = join(' ', get_body_class('no-js'));
    }

    /**
     * Sends necessary data to the view for customizer header
     * @return void
     */
    public function customizerHeader()
    {
        $headerWidgetAreas = \Municipio\Customizer\Header::enabledWidgets();

        if (is_array($headerWidgetAreas) && !empty($headerWidgetAreas)) {
            $this->data['headerLayout']['headers'] = (new \Municipio\Theme\CustomizerHeader($headerWidgetAreas))->headers;
        }

        $this->data['headerLayout']['customizer'] = true;
        $this->data['headerLayout']['template'] = apply_filters('Municipio/Controller/BaseController/customizerHeader/Template', 'customizer');

        //Old mobile menu
        $navigation = new \Municipio\Helper\Navigation();
        $this->data['navigation']['mainMenu'] = $navigation->mainMenu();
        $this->data['navigation']['mobileMenu'] = $navigation->mobileMenu();
    }

    /**
     * Sends necessary data to the view for customizer footer
     * @return void
     */
    public function customizerFooter()
    {
        $footerWidgetAreas = \Municipio\Customizer\Footer::enabledWidgets();

        if (is_array($footerWidgetAreas) && !empty($footerWidgetAreas)) {
            $this->data['footerLayout']['footers'] = (new \Municipio\Theme\CustomizerFooter($footerWidgetAreas))->footers;
        }

        $this->data['footerLayout']['customizer'] = true;
        $this->data['footerLayout']['template'] = apply_filters('Municipio/Controller/BaseController/customizerFooter/Template', 'customizer');

        //Old mobile menu
        $navigation = new \Municipio\Helper\Navigation();
        $this->data['navigation']['mainMenu'] = $navigation->mainMenu();
        $this->data['navigation']['mobileMenu'] = $navigation->mobileMenu();
    }

    public function getFixedActionBar()
    {
        $this->data['fab'] = \Municipio\Theme\FixedActionBar::getFab();
    }

    public function getFilterData()
    {
        $this->data = array_merge(
            $this->data,
            apply_filters_deprecated('Municipio/controller/base/view_data', array($this->data), "2.0", 'Municipio/ViewData')
        );
    }

    public function getHelperVariables()
    {
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

    public function getNavigationMenus()
    {
        $this->data['navigation']['headerTabsMenu'] = wp_nav_menu(array(
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

        $this->data['navigation']['headerHelpMenu'] = wp_nav_menu(array(
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
                $this->data['navigation']['mainMenu'] = $navigation->mainMenu();
                $this->data['navigation']['mobileMenu'] = $navigation->mobileMenu();

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
                $this->data['navigation']['mainMenu'] = $cache['mainMenu'];
                $this->data['navigation']['mobileMenu'] = $cache['mobileMenu'];
            }

            return;
        }

        $navigation = new \Municipio\Helper\Navigation();
        $this->data['navigation']['mainMenu'] = $navigation->mainMenu();
        $this->data['navigation']['mobileMenu'] = $navigation->mobileMenu();

        global $isSublevel;
        if ($isSublevel !== true) {
            $this->data['navigation']['sidebarMenu'] = $navigation->sidebarMenu();
        }
    }

    public function getLogotype()
    {
        $this->data['logotype'] = array(
            'standard' => get_field('logotype', 'option'),
            'negative' => get_field('logotype_negative', 'option')
        );
    }

    public function getHeaderLayout()
    {
        $headerLayoutSetting = get_field('header_layout', 'option');

        $classes = array();
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


        if (empty($headerLayoutSetting) || in_array($headerLayoutSetting, array('business', 'casual', 'contrasted-nav'))) {
            $this->data['headerLayout'] = array(
                'classes'    => implode(' ', $classes),
                'template' => 'default'
            );

            return true;
        }

        $this->data['headerLayout'] = array(
            'classes'    => implode(' ', $classes),
            'template' => $headerLayoutSetting
        );

        return true;
    }

    public function getFooterLayout()
    {
        $this->data['footerLayout'] = 'default';
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
        $this->data = apply_filters_deprecated('HbgBlade/data', array($this->data), "2.0", "Municipio/ViewData");

        //General filter
        return apply_filters('Municipio/viewData', $this->data);
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
