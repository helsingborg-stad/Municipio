<?php

namespace Municipio\Controller\Navigation\Decorators\PageTreeFallback;

class TransformPageTreeFallbackMenuItemDecorator implements PageTreeFallbackMenuItemDecoratorInterface
{
    /**
     * Add post data on post array
     *
     * @param   array   $menuItem  The post array
     *
     * @return  array   $menuItem  The post array, with appended data
     */
    public function decorate(array $menuItem, bool $fallbackToPageTree, bool $includeTopLevel, bool $onlyKeepFirstLevel): array
    {
        //Move post_title to label key
        $menuItem['label']       = $menuItem['post_title'];
        $menuItem['id']          = (int) $menuItem['ID'];
        $menuItem['post_parent'] = (int) $menuItem['post_parent'];

        //Unset data not needed
        unset($menuItem['post_title']);
        unset($menuItem['ID']);

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
            $menuItem
        );
    }
}