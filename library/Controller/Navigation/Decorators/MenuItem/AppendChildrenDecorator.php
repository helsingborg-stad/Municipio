<?php

namespace Municipio\Controller\Navigation\Decorators\MenuItem;

use Municipio\Controller\Navigation\Decorators\MenuItems\ComplementObjectsDecorator;
use Municipio\Controller\Navigation\Decorators\GetPostsByParent;
use Municipio\Controller\Navigation\Helper\GetHiddenPostIds;
use Municipio\Controller\Navigation\Helper\GetPageForPostTypeIds;

class AppendChildrenDecorator implements MenuItemDecoratorInterface
{
    public function __construct(
        private int $postId,
        private $db,
        private GetPostsByParent $getPostsByParentInstance,
        private GetHiddenPostIds $getHiddenPostIdsInstance,
        private GetPageForPostTypeIds $getPageForPostTypeIdsInstance,
        private ?ComplementObjectsDecorator $complementObjectsInstance = null
    ) {
    }

    /**
     * Sets the complement objects instance for the AppendChildrenDecorator.
     *
     * @param ComplementObjectsDecorator $complementObjectsInstance The complement objects instance to set.
     * @return void
     */
    public function setComplementObjectsInstance(ComplementObjectsDecorator $complementObjectsInstance): void
    {
        $this->complementObjectsInstance = $complementObjectsInstance;
    }

     /**
     * Check if a post has children. If this is the current post,
     * fetch the actual children array.
     *
     * @param   array   $postId    The post id
     *
     * @return  array              Flat array with parents
     */
    public function decorate(array $menuItems): array
    {
        if ($menuItems['ID'] == $this->postId) {
            $children = $this->getPostsByParentInstance->getPostsByParent(
                $menuItems['ID'],
                get_post_type($menuItems['ID'])
            );
        } else {
            $children = $this->indicateChildren($menuItems['ID']);
        }

        //If null, no children
        if (is_array($children) && !empty($children)) {
            $menuItems['children'] = $this->complementObjectsInstance->decorate($children);
        } else {
            $menuItems['children'] = (bool) $children;
        }

        //Return result
        return $menuItems;
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
        AND ID NOT IN(" . implode(", ", $this->getHiddenPostIdsInstance->get()) . ")
        LIMIT 1
      ", $postId)
        );

        //Check if posttype has content
        $pageForPostTypeIds = $this->getPageForPostTypeIdsInstance->get();
        if (array_key_exists($postId, $pageForPostTypeIds)) {
            $postTypeHasPosts = $this->db->get_var(
                $this->db->prepare("
                    SELECT ID
                    FROM " . $this->db->posts . "
                    WHERE post_parent = 0
                    AND post_status = 'publish'
                    AND post_type = %s
                    AND ID NOT IN(" . implode(", ", $this->getHiddenPostIdsInstance->get()) . ")
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
}