<?php

namespace Municipio\Helper;

use Municipio\Helper\Navigation\MenuConstructor as MenuConstructor;
use Municipio\Helper\Navigation\GetMenuData as GetMenuData;

/**
* Navigation items
*
* @author   Sebastian Thulin <sebastian.thulin@helsingborg.se>
* @since    3.0.0
* @package  Municipio\Theme
*/

class Navigation
{
    private static $db;
    private $postId         = null;
    private $cache          = [];
    private $masterPostType = 'page';
    private $identifier     = '';
    private $context        = '';

    private $cacheGroup  = 'municipioNavMenu';
    private $cacheExpire = 60 * 15; // 15 minutes
    private MenuConstructor $menuConstructorInstance;

    //Static cache for ancestors
    private static $runtimeCache = [
        'ancestors'         => [
            [
                'toplevel'   => [],
                'notoplevel' => []
            ]
        ],
        'complementObjects' => []
    ];

    public function __construct(string $identifier = '', string $context = 'municipio')
    {
        $this->identifier              = $identifier;
        $this->context                 = $context;
        $this->menuConstructorInstance = new MenuConstructor($this->identifier);
        $this->globalToLocal('wpdb', 'db');
    }

    /**
     * Store in cache
     *
     * @param string $key   The key to store in
     * @param mixed $value  The value to store
     * @return mixed
     */
    private function setCache($key, $data, $persistent = true): bool
    {
        //Runtime
        $this->cache[$key] = $data;

        //Persistent
        if ($persistent) {
            //Add to cache group (enables purging/banning)
            if ($this->setcacheGroup($key)) {
                //Store cache
                return wp_cache_set($key, $data, '', $this->cacheExpire);
            }

            return false;
        }

        return true;
    }

    /**
     * Keep track of what's has been cached
     *
     * @param string $newCacheKey
     * @return boolean
     */
    private function setCacheGroup($newCacheKey): bool
    {
        //Create new addition
        $cacheObject = [$newCacheKey];

        //Get old cache
        $previousCachedObject = wp_cache_get($this->cacheGroup);
        if (is_array($previousCachedObject) && !empty($previousCachedObject)) {
            $cacheObject = array_merge($cacheObject, $previousCachedObject);
        }

        return wp_cache_set($this->cacheGroup, array_unique($cacheObject));
    }

    /**
     * Get from cache
     *
     * @param The cache key $key
     * @return mixed
     */
    private function getCache($key, $persistent = true)
    {
        //Get runtime cache
        if (array_key_exists($key, $this->cache)) {
            return $this->cache[$key];
        }

        //Get persistent cache, store runtime
        if ($persistent) {
            return $this->cache[$key] = wp_cache_get($key);
        }

        return null;
    }

    /**
     * Get nested array representing page structure
     *
     * @param   array     $postId             The current post id
     *
     * @return  array                         Nested page array
     */
    public function getNested($postId): array
    {
        //Store current post id
        if (is_null($this->postId)) {
            $this->postId = $postId;
        }

        //Get all ancestors
        $parents = $this->getAncestors($postId, true);

        //Get all parents
        $result = $this->getItems($parents, [$this->masterPostType, get_post_type()]);

        //Format response
        $result = $this->complementObjects($result);

        //Return
        return $result;
    }

    public function getPostChildren($postId): array
    {
        //Store current post id
        if (is_null($this->postId)) {
            $this->postId = $postId;
        }

        //Page for posttype
        $pageForPostTypeIds = $this->getPageForPostTypeIds();
        if (array_key_exists($postId, $pageForPostTypeIds)) {
            $postType = $pageForPostTypeIds[$postId];
            $parentId = 0;
        } else {
            $postType = get_post_type($postId);
            $parentId = $postId;
        }

        //Get all parents
        $result = $this->getItems($parentId, $postType);

        //Format response
        $result = $this->complementObjects($result);

        //Return
        return $result;
    }

    /**
     * Check if a post has children. If this is the current post,
     * fetch the actual children array.
     *
     * @param   array   $postId    The post id
     *
     * @return  array              Flat array with parents
     */
    private function hasChildren(array $array): array
    {
        if ($array['ID'] == $this->postId) {
            $children = $this->getItems(
                $array['ID'],
                get_post_type($array['ID'])
            );
        } else {
            $children = $this->indicateChildren($array['ID']);
        }

        //If null, no children
        if (is_array($children) && !empty($children)) {
            $array['children'] = $this->complementObjects($children);
        } else {
            $array['children'] = (bool) $children;
        }

        //Return result
        return $array;
    }

    /**
     * Indicate if post has children
     *
     * @param   integer   $postId     The post id
     *
     * @return  boolean               Tells wheter the post has children or not
     */
    public function indicateChildren($postId): bool
    {
        //Define to omit error
        $postTypeHasPosts = null;

        $currentPostTypeChildren = self::$db->get_var(
            self::$db->prepare("
        SELECT ID
        FROM " . self::$db->posts . "
        WHERE post_parent = %d
        AND post_status = 'publish'
        AND ID NOT IN(" . implode(", ", $this->getHiddenPostIds()) . ")
        LIMIT 1
      ", $postId)
        );

        //Check if posttype has content
        $pageForPostTypeIds = $this->getPageForPostTypeIds();
        if (array_key_exists($postId, $pageForPostTypeIds)) {
            $postTypeHasPosts = self::$db->get_var(
                self::$db->prepare("
                    SELECT ID
                    FROM " . self::$db->posts . "
                    WHERE post_parent = 0
                    AND post_status = 'publish'
                    AND post_type = %s
                    AND ID NOT IN(" . implode(", ", $this->getHiddenPostIds()) . ")
                    LIMIT 1
                ", $pageForPostTypeIds[$postId])
            );
        }

        //Return indication boolean
        if (!is_null($currentPostTypeChildren)) {
            return true;
        } elseif (!is_null($postTypeHasPosts)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Fetch the current page/posts parent, with support for page for posttype.
     *
     * @param   array   $postId    The current post id
     *
     * @return  array              Flat array with parents
     */
    private function getAncestors(int $postId, $includeTopLevel = true): array
    {

        $cacheSubKey = $includeTopLevel ? 'toplevel' : 'notoplevel';
        if (isset(self::$runtimeCache['ancestors'][$cacheSubKey][$postId])) {
            return self::$runtimeCache['ancestors'][$cacheSubKey][$postId];
        }

        //Definitions
        $ancestorStack  = array($postId);
        $fetchAncestors = true;

        //Fetch ancestors
        while ($fetchAncestors) {
            $ancestorID = self::$db->get_var(
                self::$db->prepare("
            SELECT post_parent
            FROM  " . self::$db->posts . "
            WHERE ID = %d
            AND post_status = 'publish'
            LIMIT 1
        ", $postId)
            );

            //About to end, is there a linked pfp page?
            if ($ancestorID == 0) {
                //Get posttype of post
                $currentPostType    = get_post_type($postId);
                $pageForPostTypeIds = array_flip($this->getPageForPostTypeIds());

                //Look for replacement
                if ($currentPostType && array_key_exists($currentPostType, $pageForPostTypeIds)) {
                    $ancestorID = $pageForPostTypeIds[$currentPostType];
                }

                //No replacement found
                if ($ancestorID == 0) {
                    $fetchAncestors = false;
                }
            }

            if ($fetchAncestors !== false) {
                //Add to stack (with duplicate prevention)
                if (!in_array($ancestorID, $ancestorStack)) {
                    $ancestorStack[] = (int) $ancestorID;
                }

                //Prepare for next iteration
                $postId = $ancestorID;
            }
        }

        //Include zero level
        if ($includeTopLevel === true) {
            $ancestorStack = array_merge(
                [0],
                $ancestorStack
            );
        }

        //Return and cache result
        return self::$runtimeCache['ancestors'][$cacheSubKey][$postId] = $ancestorStack;
    }

    /**
     * Get pages/posts
     *
     * @param   integer|array  $parent    Post parent
     * @param   string|array   $postType  The post type to query
     *
     * @return  array               Array of post id:s, post_titles and post_parent
     */
    private function getItems($parent = 0, $postType = 'page'): array
    {
        //Check if if valid post type string
        if ($postType != 'all' && !is_array($postType) && !post_type_exists($postType) && is_post_type_hierarchical($postType)) {
            return [];
        }

        //Check if if valid post type array
        if (is_array($postType)) {
            $stack = [];
            foreach ($postType as $item) {
                if (post_type_exists($item) && is_post_type_hierarchical($item)) {
                    $stack[] = $item;
                }
            }

            if (empty($stack)) {
                return [];
            }

            //Get result, if one, handle as string (more efficient query)
            if (count($stack) == 1) {
                $postType = array_pop($stack);
            } else {
                $postType = $stack;
            }
        }

        //Handle post type cases
        if ($postType == 'all') {
            $postTypeSQL = "post_type IN('" . implode("', '", get_post_types(['public' => true])) . "')";
        } elseif (is_array($postType)) {
            $postTypeSQL = "post_type IN('" . implode("', '", $postType) . "')";
        } else {
            $postTypeSQL = "post_type = '" . $postType . "'";
        }

        //Support multi level query
        if (!is_array($parent)) {
            $parent = [$parent];
        }
        $parent = implode(", ", $parent);

        $sql = "
          SELECT ID, post_title, post_parent, post_type
          FROM " . self::$db->posts . "
          WHERE post_parent IN(" . $parent . ")
          AND " . $postTypeSQL . "
          AND ID NOT IN(" . implode(", ", $this->getHiddenPostIds()) . ")
          AND post_status='publish'
          ORDER BY menu_order, post_title ASC
          LIMIT 3000
        ";

        $resultSet = self::$db->get_results($sql, ARRAY_A);

        foreach ($resultSet as &$item) {
            if ($item['post_type'] != $this->masterPostType && $item['post_parent'] == 0) {
                $pageForPostTypeIds = array_flip((array) $this->getPageForPostTypeIds());

                if (array_key_exists($item['post_type'], $pageForPostTypeIds)) {
                    $item['post_parent'] = $pageForPostTypeIds[$item['post_type']];
                }
            }
        }

        //Run query
        return (array) $resultSet;
    }


    /**
     * Calculate add add data to array
     *
     * @param   array    $objects     The post array
     *
     * @return  array    $objects     The post array, with appended data
     */
    private function complementObjects(array $objects): array
    {
        if (is_array($objects) && !empty($objects)) {
            foreach ($objects as $key => $item) {
                // Generate a unique cache key for each item
                $cacheKey = md5(serialize($item));

                // Check if the item is already in the cache
                if (!isset(self::$runtimeCache['complementObjects'][$cacheKey])) {
                    // Process the item and add it to the cache
                    $processedItem = $this->transformObject(
                        $this->hasChildren(
                            $this->appendIsAncestorPost(
                                $this->appendIsCurrentPost(
                                    $this->customTitle(
                                        $this->appendHref($item)
                                    )
                                )
                            )
                        )
                    );

                    // Store the processed item in the cache
                    self::$runtimeCache['complementObjects'][$cacheKey] = apply_filters(
                        'Municipio/Navigation/Item',
                        $processedItem,
                        $this->identifier,
                        false
                    );
                }

                // Use the cached item
                $objects[$key] = self::$runtimeCache['complementObjects'][$cacheKey];
            }
        }

        return $objects;
    }

    /**
     * Add post is ancestor data on post array
     *
     * @param   object   $array         The post array
     *
     * @return  array    $postArray     The post array, with appended data
     */
    private function appendIsAncestorPost(array $array): array
    {
        if (in_array($array['ID'], $this->getAncestors($this->postId))) {
            $array['ancestor'] = true;
        } else {
            $array['ancestor'] = false;
        }

        return $array;
    }

    /**
     * Add post is current data on post array
     *
     * @param   object   $array         The post array
     *
     * @return  array    $postArray     The post array, with appended data
     */
    private function appendIsCurrentPost(array $array): array
    {
        if ($array['ID'] == $this->postId) {
            $array['active'] = true;
        } elseif (\Municipio\Helper\IsCurrentUrl::isCurrentUrl($array['href'])) {
            $array['active'] = true;
        } else {
            $array['active'] = false;
        }

        return $array;
    }

    /**
     * Add post href data on post array
     *
     * @param   object   $array         The post array
     * @param   boolean  $leavename     Leave name wp default param
     *
     * @return  array    $postArray     The post array, with appended data
     */
    private function appendHref(array $array, bool $leavename = false): array
    {
        $array['href'] = get_permalink($array['ID'], $leavename);

        return $array;
    }

    /**
     * Add post data on post array
     *
     * @param   array   $array  The post array
     *
     * @return  array   $array  The post array, with appended data
     */
    private function transformObject(array $array): array
    {
        //Move post_title to label key
        $array['label']       = $array['post_title'];
        $array['id']          = (int) $array['ID'];
        $array['post_parent'] = (int) $array['post_parent'];

        //Unset data not needed
        unset($array['post_title']);
        unset($array['ID']);

        //Sort & return
        return array_merge(
            array(
                'id'          => null,
                'post_parent' => null,
                'post_type'   => null,
                'active'      => null,
                'ancestor'    => null,
                'label'       => null,
                'href'        => null,
                'children'    => null
            ),
            $array
        );
    }

    /**
     * Get a list of hidden post id's
     *
     * Optimzing: It may be faster on smaller databases
     * to not use a join. This will however slow down larger sites.
     *
     * This is a calculated risk that should be caught
     * by the object cache. Tests have been made to enshure
     * good performance.
     *
     * @param string $metaKey The meta key to get data from
     *
     * @return array
     */
    private function getHiddenPostIds(string $metaKey = "hide_in_menu"): array
    {
        //Get cached result
        $cache = $this->getCache($metaKey);
        if (!is_null($cache) && is_array($cache)) {
            return $cache;
        }

        //Get meta
        $hiddenPages = (array) self::$db->get_col(
            self::$db->prepare(
                "
                SELECT post_id
                FROM " . self::$db->postmeta . " AS pm 
                JOIN " . self::$db->posts . " AS p ON pm.post_id = p.ID
                WHERE meta_key = %s
                AND meta_value = '1'
                AND post_status = 'publish'
            ",
                $metaKey
            )
        );

        //Do not let the array return be empty
        if (empty($hiddenPages)) {
            //Declare result
            $hiddenPages = [PHP_INT_MAX];
        }

        //Cache
        $this->setCache($metaKey, $hiddenPages);

        return $hiddenPages;
    }

    /**
     * Get a list of custom page titles
     *
     * Optimzing: It may be faster on smaller databases
     * to not use a join. This will however slow down larger sites.
     *
     * This is a calculated risk that should be caught
     * by the object cache. Tests have been made to enshure
     * good performance.
     *
     * @param string $metaKey The meta key to get data from
     *
     * @return array
     */
    private function getMenuTitle(string $metaKey = "custom_menu_title"): array
    {
        //Get cached result
        $cache = $this->getCache($metaKey);
        if (!is_null($cache) && is_array($cache)) {
            return $cache;
        }

        //Get meta
        $result = (array) self::$db->get_results(
            self::$db->prepare(
                "
                SELECT post_id, meta_value
                FROM " . self::$db->postmeta . " as pm
                JOIN " . self::$db->posts . " AS p ON pm.post_id = p.ID
                WHERE meta_key = %s
                AND meta_value != ''
                AND post_status = 'publish'
            ",
                $metaKey
            )
        );

        //Declare result
        $pageTitles = [];

        //Add visible page ids
        if (is_array($result) && !empty($result)) {
            foreach ($result as $result) {
                if (empty($result->meta_value)) {
                    continue;
                }
                $pageTitles[$result->post_id] = $result->meta_value;
            }
        }

        //Cache
        $this->setCache($metaKey, $pageTitles);

        return $pageTitles;
    }

    /**
     * Replace native title with custom menu name
     *
     * @param array $array
     *
     * @return object
     */
    private function customTitle(array $array): array
    {
        $customTitles = $this->getMenuTitle();

        //Get custom title
        if (isset($customTitles[$array['ID']])) {
            $array['post_title'] = $customTitles[$array['ID']];
        }

        //Replace empty titles
        if ($array['post_title'] == "") {
            $array['post_title'] = __("Untitled page", 'municipio');
        }

        return $array;
    }

    /**
     * Get WordPress menu items (from default menu management)
     *
     * @param string $menu The menu id to get
     * @return bool|array
     */
    public function getMenuItems(int|string $menu, int $pageId = null, bool $fallbackToPageTree = false, bool $includeTopLevel = true, bool $onlyKeepFirstLevel = false)
    {
        $menuItems = GetMenuData::getNavMenuItems($menu) ?: [];
        $menuItems = $this->menuConstructorInstance->structureMenuItems($menuItems, $pageId);

        if (empty($menuItems) && $fallbackToPageTree && is_numeric($pageId)) {
            $menuItems = $this->getNested($pageId);
        }

        $menuItems = apply_filters('Municipio/Navigation/Items', $menuItems, $this->identifier);

        if (!empty($menuItems)) {
            $pageStructure = $includeTopLevel ?
                $this->menuConstructorInstance->buildStructuredMenu($menuItems) :
                $this->removeTopLevel($this->menuConstructorInstance->buildStructuredMenu($menuItems));

            if ($onlyKeepFirstLevel) {
                $pageStructure = $this->removeSubLevels($pageStructure);
            }


            $menuItems = apply_filters('Municipio/Navigation/Nested', $pageStructure, $this->identifier, $pageId);

            if ($menu === 'secondary-menu' && !empty($menuItems)) {
                $menuItems = $this->menuConstructorInstance->structureMenu($menuItems, $menu);
            }

            return $menuItems;
        }

        return false;
    }

    /**
     * Removes top level items
     *
     * @param   array   $result    The unfiltered result set
     *
     * @return  array   $result    The filtered result set (without top level)
     */
    public function removeTopLevel(array $result): array
    {
        foreach ($result as $item) {
            if ($item['ancestor'] == true && is_array($item['children'])) {
                return $item['children'];
            }
        }
        return [];
    }

    /**
     * Removes sub level items
     *
     * @param   array   $result    The unfiltered result set
     *
     * @return  array   $result    The filtered result set (without sub levels)
     */
    public function removeSubLevels(array $result): array
    {
        foreach ($result as $key => $item) {
            $result[$key]['children'] = false;
        }
        return $result;
    }
    /**
     * It returns an array of items that are used in the accessibility menu.
     *
     * @return An array of items.
     */
    public function getAccessibilityItems()
    {
        $items = [];
        if (is_single() || is_page()) {
            $items['print'] =  array(
                'icon'   => 'print',
                'href'   => '#',
                'script' => 'window.print();return false;',
                'text'   => __('Print', 'municipio'),
                'label'  => __('Print this page', 'municipio')
            );
        }

        $items = apply_filters_deprecated('accessibility_items', [$items], '3.0.0', 'Municipio/Accessibility/Items');

        return apply_filters('Municipio/Accessibility/Items', $items);
    }
    /**
     * BreadCrumbData
     * Fetching data for breadcrumbs
     * @return array|void
     * @throws \Exception
     */
    public function getBreadcrumbItems($pageId)
    {
        global $post;

        if (!is_a($post, 'WP_Post')) {
            return;
        }

        //Define data storage
        $pageData = [];

        //Homepage
        $pageData[get_option('page_on_front')] = array(
            'label'   => __("Home"),
            'href'    => get_home_url(),
            'current' => is_front_page() ? true : false,
            'icon'    => 'home'
        );

        $queriedObj         = get_queried_object();
        $archiveLink        = get_post_type_archive_link(get_post_type($queriedObj));
        $pageForPostTypeIds = array_flip($this->getPageForPostTypeIds());

        if ($archiveLink) {
            $label = get_post_type_object(get_post_type($queriedObj))->label ?? __("Untitled page", 'municipio');

            if (is_archive()) {
                $label = $queriedObj->label ?? __("Untitled page", 'municipio');
            }

            array_push($pageData, array(
                    'label'   => __($label),
                    'href'    => $archiveLink,
                    'current' => false,
                    'icon'    => 'chevron_right'
            ));
        }

        if (!is_front_page() && !is_archive()) {
            //Get all ancestors to page
            $ancestors = $this->getAncestors($pageId);

            //Create dataset
            if (is_countable($ancestors)) {
                $ancestors = array_reverse($ancestors);
                array_pop($ancestors);

                //Add items
                foreach ($ancestors as $id) {
                    if (!in_array($id, $pageForPostTypeIds)) {
                        $title                    = WP::getTheTitle($id);
                        $pageData[$id]['label']   = $title ? $title : __("Untitled page", 'municipio');
                        $pageData[$id]['href']    = WP::getPermalink($id);
                        $pageData[$id]['current'] = false;
                        $pageData[$id]['icon']    = 'chevron_right';
                    }
                }
            }
        }

        //Apply filters
        return apply_filters('Municipio/Breadcrumbs/Items', $pageData, $queriedObj, $this->context);
    }

    /**
     * Get all post id's mapped as a post type container.
     *
     * @return array
     */
    public function getPageForPostTypeIds(): array
    {
        //Get cached result
        $cache = $this->getCache('pageForPostType');
        if (!is_null($cache) && is_array($cache)) {
            return $cache;
        }

        //Declare results array
        $result = array();

        //Only supported for hierarchical
        $postTypes = get_post_types([
            'public'       => true,
            'hierarchical' => true
        ]);

        //Check for results
        if (is_countable($postTypes)) {
            foreach ($postTypes as $postType) {
                //Fetch mapping ID
                $postId = get_option('page_for_' . $postType, true);

                //Validate mapping ID
                if (is_numeric($postId)) {
                    $result[$postId] = $postType;
                }
            }
        }

        //Cache
        $this->setCache('pageForPostType', $result);

        return $result;
    }

    /**
     * Creates a local copy of the global instance
     * The target var should be defined in class header as private or public
     *
     * @param string $global The name of global varable that should be made local
     * @param string $local Handle the global with the name of this string locally
     *
     * @return void
     */
    private function globalToLocal($global, $local = null)
    {
        global $$global;
        if (is_null($local)) {
            self::$$global = $$global;
        } else {
            self::$$local = $$global;
        }
    }

    /**
     * Returns the placement of quicklinks for a given post.
     *
     * @param int $postId A WP post ID.
     *
     * @return bool|string The value of the 'quicklinks_placement' field for the given post.
     */
    public static function getQuicklinksPlacement(int $postId = 0)
    {
        if (!function_exists('get_field')) {
            return false;
        }
        return apply_filters(
            'Municipio/Helper/Navigation/getQuicklinksPlacement',
            get_field('quicklinks_placement', $postId),
            $postId
        );
    }
    /**
     * Returns a boolean value based on whether the quicklinks should be displayed
     * below the content or not.
     *
     * @param int $postId A WP post ID.
     *
     * @return boolean True if the quicklinks should be displayed below the content.
     */
    public static function displayQuicklinksAfterContent(int $postId = 0)
    {
        if (!function_exists('get_field')) {
            return false;
        }
        return apply_filters(
            'Municipio/Helper/Navigation/displayQuicklinksAfterContent',
            self::getQuicklinksPlacement($postId) === 'below_content' ? true : false,
            $postId
        );
    }
}
