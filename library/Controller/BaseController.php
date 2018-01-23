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
        $customizer = apply_filters('Municipio/Controller/BaseController/Customizer', false);

        if ($customizer) {
             $this->customizerHeader();
        } else {
            $this->getLogotype();
            $this->getHeaderLayout();
            $this->getFooterLayout();
            $this->getNavigationMenus();
        }

        $this->getHelperVariables();
        $this->getFilterData();
        $this->getVerticalMenu();
        $this->getFixedActionBar();

        $this->init();
    }

    /**
     * Sends necessary data to the view for customizer header
     * @return array | false
     */
    public function customizerHeader()
    {
        $this->data['headerLayout']['customizer'] = true;
        $this->data['headerLayout']['template'] = apply_filters('Municipio/Controller/BaseController/customizerHeader/Template', 'customizer');
        $this->data['headerLayout']['classes'] = apply_filters('Municipio/Controller/BaseController/customizerHeader/Classes', 'c-site-header t-site-header');
        $this->data['headerLayout']['attributes'] = apply_filters('Municipio/Controller/BaseController/customizerHeader/Attributes', '');
        $this->data['headerLayout']['panels'] = $this->mapCustomizerHeader();
    }

    /**
     * Returns mapped array that builds up the customizer header
     * @return array | false
     */
    public function mapCustomizerHeader()
    {
        $panelRows = array();
        $panelItems = \Municipio\Customizer\Header\HeaderPanel::getHeaderWidgetAreas();

        if (!is_array($panelItems) || empty($panelItems)) {
            return false;
        }

        $classes = array(
            'item' => 'c-site-header__panel_item',
            'itemModifier' => 'c-site-header__panel_item--',
            'row' => 'c-site-header__panel',
            'rowModifier' => 'c-site-header__panel--',
            'rowBody' => 'c-site-header__panel_row container'
        );

        $classes = apply_filters('Municipio/Controller/BaseController/mapCustomizerHeader/classes', $classes);

        //Map row & items
        foreach ($panelItems as $panelItem) {
            $panelRows[$panelItem['position']]['items'][] = array(
                'id' => $panelItem['id'],
                'classes' => $classes['item'] . ' ' . $classes['itemModifier'] . $panelItem['alignment']
            );
        }

        //Add classes & attributes to row
        foreach ($panelRows as $position => $panelRow) {
            $panelRows[$position]['classes'] = $classes['row'] . ' ' . $classes['rowModifier'] . $position;
            $panelRows[$position]['bodyClasses'] = $classes['rowBody'];
            $panelRows[$position]['attributes'] = "";
        }

        $panelRows = apply_filters('Municipio/Controller/BaseController/mapCustomizerHeader/rows', $panelRows);

        if (!empty($panelRows)) {
            return $panelRows;
        }

        return false;
    }

    public function getFixedActionBar()
    {
        $this->data['fab'] = \Municipio\Theme\FixedActionBar::getFab();
    }

    public function getFilterData()
    {
        $this->data = array_merge(
            $this->data,
            apply_filters('Municipio/controller/base/view_data', $this->data)
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
     * Bind to a custom template file
     * @return void
     */
    public static function registerTemplate()
    {
        // \Municipio\Helper\Template::add('Front page', 'front-page.blade.php');
    }

    /**
     * Returns the data
     * @return array Data
     */
    public function getData()
    {
        return apply_filters('HbgBlade/data', $this->data);
    }

    /**
     * Creates a local copy of the global instance
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
