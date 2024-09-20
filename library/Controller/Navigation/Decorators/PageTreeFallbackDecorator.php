<?php

namespace Municipio\Controller\Navigation\Decorators;

use Municipio\Controller\Navigation\Cache\CacheManagerInterface;
use Municipio\Controller\Navigation\Decorators\GetAncestors;
use Municipio\Controller\Navigation\Decorators\GetPostsByParent;

class PageTreeFallbackDecorator implements MenuItemsDecoratorInterface
{
    private $masterPostType = 'page';
    private ?int $postId = null;

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

    public function __construct(
        private string $identifier, 
        private int|false $id, 
        private string|false $name, 
        private int $pageId,
        private $db,
        private CacheManagerInterface $cacheManager,
        private CacheManagerInterface $runtimeCacheInstance,
        private GetAncestors $getAncestorsInstance,
        private GetPostsByParent $getPostsByParentInstance,
    ) {
    }

    public function decorate(array $menuItems, bool $fallbackToPageTree, bool $includeTopLevel, bool $onlyKeepFirstLevel): array
    {
        if (empty($menuItems) && $fallbackToPageTree && is_numeric($this->pageId)) {
            $menuItems = $this->getNested($includeTopLevel);
        }

        return $menuItems;
    }

    private function getNested(bool $includeTopLevel): array
    {
        //Store current post id
        if (is_null($this->postId)) {
            $this->postId = $this->pageId;
        }

        //Get all ancestors
        $result = $this->getAncestorsInstance->getAncestors($includeTopLevel);

        //Get all parents
        $result = $this->getPostsByParentInstance->getPostsByParent($result, [$this->masterPostType, get_post_type()]);

        //Format response
        $result = $this->complementObjects($result);

        //Return
        return $result;
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

        $currentPostTypeChildren = $this->db->get_var(
            $this->db->prepare("
        SELECT ID
        FROM " . $this->db->posts . "
        WHERE post_parent = %d
        AND post_status = 'publish'
        AND ID NOT IN(" . implode(", ", $this->getHiddenPostIds()) . ")
        LIMIT 1
      ", $postId)
        );

        //Check if posttype has content
        $pageForPostTypeIds = $this->getPageForPostTypeIds();
        if (array_key_exists($postId, $pageForPostTypeIds)) {
            $postTypeHasPosts = $this->db->get_var(
                $this->db->prepare("
                    SELECT ID
                    FROM " . $this->db->posts . "
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
        $cache = $this->cacheManager->getCache($metaKey);
        if (!is_null($cache) && is_array($cache)) {
            return $cache;
        }

        //Get meta
        $result = (array) $this->db->get_results(
            $this->db->prepare(
                "
                SELECT post_id, meta_value
                FROM " . $this->db->postmeta . " as pm
                JOIN " . $this->db->posts . " AS p ON pm.post_id = p.ID
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
        $this->cacheManager->setCache($metaKey, $pageTitles);

        return $pageTitles;
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
     * Add post is ancestor data on post array
     *
     * @param   object   $array         The post array
     *
     * @return  array    $postArray     The post array, with appended data
     */
    private function appendIsAncestorPost(array $array): array
    {
        if (in_array($array['ID'], $this->getAncestorsInstance->getAncestors())) {
            $array['ancestor'] = true;
        } else {
            $array['ancestor'] = false;
        }

        return $array;
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
            $children = $this->getPostsByParentInstance->getPostsByParent(
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
        $cache = $this->cacheManager->getCache($metaKey);
        if (!is_null($cache) && is_array($cache)) {
            return $cache;
        }

        //Get meta
        $hiddenPages = (array) $this->db->get_col(
            $this->db->prepare(
                "
                SELECT post_id
                FROM " . $this->db->postmeta . " AS pm 
                JOIN " . $this->db->posts . " AS p ON pm.post_id = p.ID
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
        $this->cacheManager->setCache($metaKey, $hiddenPages);

        return $hiddenPages;
    }

    /**
     * Get all post id's mapped as a post type container.
     *
     * @return array
     */
    public function getPageForPostTypeIds(): array
    {
        //Get cached result
        $cache = $this->cacheManager->getCache('pageForPostType');
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
        $this->cacheManager->setCache('pageForPostType', $result);

        return $result;
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
}
