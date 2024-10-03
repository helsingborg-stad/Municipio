<?php

namespace Municipio\Theme;

use Municipio\SchemaData\Utils\GetEnabledSchemaTypesInterface;

/**
 * Class Navigation
 * @package Municipio\Theme
 */
class Navigation
{
    /**
     * Navigation constructor.
     */
    public function __construct(private GetEnabledSchemaTypesInterface $getEnabledSchemaTypes)
    {
        add_action('init', array($this, 'registerNavigationMenus'), 15, 2);
        add_filter('Municipio/Navigation/Item', array($this, 'appendFetchUrl'), 10, 2);
        add_filter('Municipio/Navigation/Item', array($this, 'forceItemStyleTiles'), 10, 2);
        add_filter('Municipio/Navigation/Item', array($this, 'forceItemStyleButtons'), 10, 2);
    }

    public static function getMenuLocations()
    {
        return array(
            'help-menu'           => __('Help menu', 'municipio'),
            'header-tabs-menu'    => __('Header tabs menu', 'municipio'),
            'main-menu'           => __('Primary menu', 'municipio'),
            'secondary-menu'      => __('Secondary menu & drawer menu', 'municipio'),
            'mega-menu'           => __('Mega menu', 'municipio'),
            'dropdown-links-menu' => __('Dropdown menu', 'municipio'),
            'floating-menu'       => __('Floating menu', 'municipio'),
            'language-menu'       => __('Language menu', 'municipio'),
            'quicklinks-menu'     => __('Quicklinks menu', 'municipio'),
            'mobile-drawer'       => __('Drawer (bottom)', 'municipio'),
            'siteselector-menu'   => __('Siteselector', 'municipio'),
        );
    }

    /**
     * Force the item style to "tiles" on selected menus
     */
    public function forceItemStyleTiles($item, $identifier)
    {
        $targetMenuIdentifiers = ['language', 'floating'];

        if (!in_array($identifier, $targetMenuIdentifiers)) {
            return $item;
        }

        if (isset($item['style'])) {
            $item['style'] = 'tiles';
        }

        return $item;
    }

    /**
     * Force the item style to "tiles" on selected menus
     */
    public function forceItemStyleButtons($item, $identifier)
    {
        $targetMenuIdentifiers = ['tab'];

        if (!in_array($identifier, $targetMenuIdentifiers)) {
            return $item;
        }

        if (isset($item['style'])) {
            $item['style'] = 'button';
        }

        return $item;
    }

    /**
     * Register Menus
     */
    public function registerNavigationMenus()
    {
        $menus = self::getMenuLocations();

        //Append dynamic menus
        $menus = array_merge($menus, $this->getArchiveMenus());
        $menus = array_merge($menus, $this->getSchemaTypeMenus());

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
        $archiveMenu     = array();
        $publicPostTypes = \Municipio\Helper\PostType::getPublic();

        if (is_array($publicPostTypes) && !empty($publicPostTypes)) {
            foreach ($publicPostTypes as $postTypeSlug => $postType) {
                if ($postType->has_archive !== true && ($postTypeSlug != 'post' && !get_post_type_archive_link($postTypeSlug))) {
                    continue;
                }

                $archiveMenu[$postType->name . '-menu']           = implode(
                    ' ',
                    array(
                        $postType->label,
                        __("(above archive posts)", "municipio")
                    )
                );
                $archiveMenu[$postType->name . '-secondary-menu'] = implode(
                    ' ',
                    array(
                        $postType->label,
                        __("(sidebar)", "municipio")
                    )
                );
            }
        }

        return $archiveMenu;
    }

    /**
     * Get all schema types menus.
     * Create a menu specification for each of these.
     *
     * @return array
     */
    private function getSchemaTypeMenus(): array
    {
        $schemaTypeMenus    = array();
        $enabledSchemaTypes = $this->getEnabledSchemaTypes->getEnabledSchemaTypesAndProperties();

        if (!empty($enabledSchemaTypes)) {
            foreach ($enabledSchemaTypes as $type => $props) {
                $schemaTypeMenus[strtolower($type) . '-secondary-menu'] = sprintf(
                    __('Content type - %s (sidebar)', 'municipio'),
                    $type
                );
            }
        }

        return $schemaTypeMenus;
    }

    public function appendFetchUrl($item, $identifier)
    {

        $targetMenuIdentifiers = [
            'mobile' => 'mobile', 
            'primary' => 'mobile',
            'sidebar' => 'sidebar'
        ];

        if (!array_key_exists($identifier, $targetMenuIdentifiers)) {
            return $item;
        }

        if (isset($item['id']) && is_numeric($item['id'])) {
            $depth = $this->getPageDepth($item['id']) + 1;
        } else {
            $depth = 1;
        }

        $dataFetchUrl = apply_filters(
            'Municipio/homeUrl',
            esc_url(get_home_url())
        )   . '/wp-json/municipio/v1/navigation/children/render'
            . '?' . 'pageId=' .  $item['id'] . '&viewPath=' . 'partials.navigation.'
            . $targetMenuIdentifiers[$identifier] . '&identifier=' . $targetMenuIdentifiers[$identifier] . '&depth=' . $depth;

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
    private function getPageDepth($postId, $depth = 1)
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
            $page     = get_post($parentId);
            $parentId = $page->post_parent;
            $depth++;
        }
        return $depth;
    }
}
