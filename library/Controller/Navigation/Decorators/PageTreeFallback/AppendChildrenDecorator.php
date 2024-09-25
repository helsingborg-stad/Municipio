<?php

namespace Municipio\Controller\Navigation\Decorators\PageTreeFallback;

use Municipio\Controller\Navigation\Decorators\PageTreeFallback\ComplementPageTreeDecorator;
use Municipio\Controller\Navigation\Helper\GetPostsByParent;
use Municipio\Controller\Navigation\Helper\GetHiddenPostIds;
use Municipio\Controller\Navigation\Helper\GetPageForPostTypeIds;

class AppendChildrenDecorator implements PageTreeFallbackMenuItemDecoratorInterface
{
    public function __construct(
        private int $postId,
        private $db,
        private GetPostsByParent $getPostsByParentInstance,
        private GetHiddenPostIds $getHiddenPostIdsInstance,
        private GetPageForPostTypeIds $getPageForPostTypeIdsInstance,
        private ?ComplementPageTreeDecorator $complementPageTreeDecoratorInstance = null
    ) {
    }

    /**
     * Sets the complement objects instance for the AppendChildrenDecorator.
     *
     * @param ComplementPageTreeDecorator $complementObjectsInstance The complement objects instance to set.
     * @return void
     */
    public function setComplementPageTreeDecoratorInstance(ComplementPageTreeDecorator $complementPageTreeDecoratorInstance): void
    {
        $this->complementPageTreeDecoratorInstance = $complementPageTreeDecoratorInstance;
    }

     /**
     * Check if a post has children. If this is the current post,
     * fetch the actual children array.
     *
     * @param   array   $postId    The post id
     *
     * @return  array              Flat array with parents
     */
    public function decorate(array|object $menuItem, bool $fallbackToPageTree, bool $includeTopLevel, bool $onlyKeepFirstLevel): array
    {
        if ($menuItem['id'] == $this->postId) {
            $children = $this->getPostsByParentInstance->getPostsByParent(
                $menuItem['id'],
                get_post_type($menuItem['id'])
            );
        } else {
            $children = $this->indicateChildren($menuItem['id']);
        }

        //If null, no children
        if (is_array($children) && !empty($children)) {
            $menuItem['children'] = $this->complementPageTreeDecoratorInstance->decorate($children, $fallbackToPageTree, $includeTopLevel, $onlyKeepFirstLevel);
        } else {
            $menuItem['children'] = (bool) $children;
        }

        //Return result
        return $menuItem;
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