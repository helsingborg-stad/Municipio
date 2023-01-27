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
        add_action('init', array($this, 'registerNavigationMenus'), 15, 2);
        add_filter('Municipio/Navigation/Item', array($this, 'appendFetchUrl'), 10, 2);
    }

    public static function getMenuLocations() {
        return array(
            'help-menu' => __('Help menu', 'municipio'),
            'header-tabs-menu' => __('Header tabs menu', 'municipio'),
            'main-menu' => __('Primary menu', 'municipio'),
            'secondary-menu' => __('Secondary menu & mobile menu', 'municipio'),
            'hamburger-menu' => __('Hamburger menu', 'municipio'),
            'dropdown-links-menu' => __('Dropdown menu', 'municipio'),
            'floating-menu' => __('Floating menu', 'municipio'),
            'language-menu' => __('Language menu', 'municipio'),
            'quicklinks-menu' => __('Quicklinks menu', 'municipio'),
            'mobile-drawer' => __('Mobile drawer (bottom)', 'municipio'),
        );
    }

    /**
     * Register Menus
     */
    public function registerNavigationMenus()
    {
        $menus = self::getMenuLocations();

        //Append dynamic menus
        $menus = array_merge($menus, $this->getArchiveMenus());

        //Register menus
        register_nav_menus($menus);
    }

    /**
     * Get all post types where a archive page exits.
     * Create a menu specification for each of these.
     *
     * @return array
     */
    private function getArchiveMenus(): array
    {
        $archiveMenu = array();
        $publicPostTypes = \Municipio\Helper\PostType::getPublic();

        if (is_array($publicPostTypes) && !empty($publicPostTypes)) {
            foreach ($publicPostTypes as $postType) {
                if ($postType->has_archive !== true) {
                    continue;
                }

                $archiveMenu[$postType->name . '-menu'] = implode(
                    ' ',
                    array(
                        $postType->label,
                        __("(above archive posts)", "municipio")
                    )
                );
            }
        }

        return $archiveMenu;
    }

    public function appendFetchUrl($item, $identifier)
    {
        $targetMenuIdentifiers = ['mobile', 'sidebar'];

        if (!in_array($identifier, $targetMenuIdentifiers)) {
            return $item;
        }

        if (isset($item['id']) && is_numeric($item['id'])) {
            $depth = $this->getPageDepth($item['id']) + 1;
        } else {
            $depth = 0;
        }

        $dataFetchUrl = apply_filters(
            'Municipio/homeUrl',
            esc_url(get_home_url())
        )   . '/wp-json/municipio/v1/navigation/children/render'
            . '?' . 'pageId=' .  $item['id'] . '&viewPath=' . 'partials.navigation.'
            . $identifier . '&identifier=' . $identifier . '&depth=' . $depth;

        $item['attributeList'] = array(
            'data-fetch-url' => $dataFetchUrl
        );

        return $item;
    }

    /**
     * Get depth of page
     *
     * @param int $postId

     * @return int The depth of the page
     */
    private function getPageDepth($postId, $depth = 0)
    {
        $object = get_post($postId);

        //Not found, fake 0
        if (!is_a($object, 'WP_Post')) {
            return 0;
        }

        //Set post parent
        $parentId = $object->post_parent;

        //Get depth
        while ($parentId > 0) {
            $page = get_post($parentId);
            $parentId = $page->post_parent;
            $depth++;
        }
        return $depth;
    }
}
