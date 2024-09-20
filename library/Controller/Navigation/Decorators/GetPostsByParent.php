<?php

namespace Municipio\Controller\Navigation\Decorators;

use Municipio\Controller\Navigation\Helper\GetHiddenPostIds;
use Municipio\Controller\Navigation\Helper\GetPageForPostTypeIds;

class GetPostsByParent
{
    private $masterPostType = 'page';

    public function __construct(
        private $db, 
        private GetHiddenPostIds $getHiddenPostIdsInstance, 
        private GetPageForPostTypeIds $getPageForPostTypeIdsInstance
    ) {
    }

    /**
     * Retrieve hierarchical posts/pages based on specified parent and post type(s).
     *
     * This method fetches posts or pages that match a given parent ID and post type criteria.
     * It supports both single and multiple post types, and can return top-level or child posts
     * depending on the provided parent. Results are ordered by menu order and post title.
     * Additionally, it handles special cases where custom post types are assigned to specific pages.
     *
     * @param   integer|array  $parent    ID or array of IDs of the parent post(s) to query. Defaults to 0 (top-level).
     * @param   string|array   $postType  Single post type or an array of post types to query. Use 'all' to fetch all public types. Defaults to 'page'.
     *
     * @return  array  Array of posts including their ID, title, parent ID, and post type.
     */
    public function getPostsByParent(int|array $parent = 0, string|array $postType = 'page'): array
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
          FROM " . $this->db->posts . "
          WHERE post_parent IN(" . $parent . ")
          AND " . $postTypeSQL . "
          AND ID NOT IN(" . implode(", ", $this->getHiddenPostIdsInstance->get()) . ")
          AND post_status='publish'
          ORDER BY menu_order, post_title ASC
          LIMIT 3000
        ";

        $resultSet = $this->db->get_results($sql, ARRAY_A);

        foreach ($resultSet as &$item) {
            if ($item['post_type'] != $this->masterPostType && $item['post_parent'] == 0) {
                $pageForPostTypeIds = array_flip((array) $this->getPageForPostTypeIdsInstance->get());

                if (array_key_exists($item['post_type'], $pageForPostTypeIds)) {
                    $item['post_parent'] = $pageForPostTypeIds[$item['post_type']];
                }
            }
        }

        //Run query
        return (array) $resultSet;
    }
}