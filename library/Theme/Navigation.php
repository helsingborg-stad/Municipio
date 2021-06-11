<?php

namespace Municipio\Theme;

/**
 * Class Navigation
 * @package Municipio\Theme
 */
class Navigation
{

    /**
     * Navigation constructor.
     */
    public function __construct()
    {
        //Register all menus
        $this->registerMenus();

        //Adds language in the end of the menu
        add_filter('Municipio/Navigation/Nested', array($this, 'addLanguageMenuItem'), 20, 3);
    }

    /**
     * Adds language icon to main menu
     *
     * @param array     $data          Array containing the menu
     * @param string    $identifier    What menu being filtered
     * 
     * @return array
     */
    public function addLanguageMenuItem($data, $identifier, $pageId) {
                
        if($identifier == "primary") {

            $languageMenu       = new \Municipio\Helper\Navigation('language');
            $languageMenuItems  =  $languageMenu->getMenuItems('language-menu', $pageId, false, true, true);

            if(is_array($languageMenuItems) && !empty($languageMenuItems)) {
                $data[] = [
                    "id" => "language", 
                    "post_parent" => null,
                    "post_type" => null,
                    "active" => false, 
                    "ancestor" => false,
                    "children" => $languageMenuItems,
                    "label" => "",
                    "href" => "#language",
                    "icon" => ['icon' => 'language', 'size' => 'md'],
                    "attributeList" => [
                        'aria-label' => __("Select language", 'municipio'),
                    ],
                ]; 
            }
        }

        return $data; 
    }

    /**
     * Register Menus
     */
    public function registerMenus()
    {
        $menus = array(
            'help-menu' => __('Help menu', 'municipio'),
            'header-tabs-menu' => __('Header tabs menu', 'municipio'),
            'main-menu' => __('Primary menu', 'municipio'),
            'secondary-menu' => __('Secondary menu & mobile menu', 'municipio'),
            'dropdown-links-menu' => __('Dropdown menu', 'municipio'),
            'floating-menu' => __('Floating menu', 'municipio'),
            'language-menu' => __('Language menu', 'municipio'),
        );

        register_nav_menus($menus);
    }

}
