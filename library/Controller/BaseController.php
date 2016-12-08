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
        $this->getLogotype();
        $this->getHeaderLayout();
        $this->getFooterLayout();
        $this->getNavigationMenus();
        $this->getHelperVariables();
        $this->getFilterData();

        $this->init();
    }

    public function getFilterData()
    {
        $this->data = array_merge(
            $this->data,
            apply_filters('Municipio/controller/base/view_data', array())
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
        $navigation = new \Municipio\Helper\Navigation();
        $this->data['navigation']['mainMenu'] = $navigation->mainMenu();
        $this->data['navigation']['mobileMenu'] = $navigation->mobileMenu();
        $this->data['navigation']['sidebarMenu'] = $navigation->sidebarMenu();
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

        if (empty($headerLayoutSetting) || in_array($headerLayoutSetting, array('business', 'casual', 'contrasted-nav'))) {
            $this->data['headerLayout'] = array(
                'class'    => 'header-'.$headerLayoutSetting,
                'template' => 'default'
            );

            return true;
        }

        $this->data['headerLayout'] = array(
            'class'    => 'header-' . $headerLayoutSetting,
            'template' => $headerLayoutSetting
        );

        return true;
    }

    public function getFooterLayout()
    {
        switch (get_field('footer_layout', 'option')) {
            case 'compressed':
                $this->data['footerLayout'] = 'compressed';
                break;

            default:
                $this->data['footerLayout'] = 'default';
                break;
        }
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
}
