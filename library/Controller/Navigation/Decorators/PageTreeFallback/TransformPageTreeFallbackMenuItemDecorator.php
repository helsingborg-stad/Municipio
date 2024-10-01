<?php

namespace Municipio\Controller\Navigation\Decorators\PageTreeFallback;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;

class TransformPageTreeFallbackMenuItemDecorator implements PageTreeFallbackMenuItemDecoratorInterface
{
    /**
     * Decorates a menu item in the page tree fallback navigation.
     *
     * @param array $menuItem The menu item to decorate.
     * @param MenuConfigInterface $menuConfig The menu configuration.
     * @param ComplementPageTreeDecorator $parentInstance The parent instance of the decorator.
     * @return array The decorated menu item.
     */
    public function decorate(array $menuItem, MenuConfigInterface $menuConfig, ComplementPageTreeDecorator $parentInstance): array
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
