<?php

namespace Municipio\Controller\Navigation\Decorators\PageTreeFallback;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;

class AppendIsCurrentPostDecorator implements PageTreeFallbackMenuItemDecoratorInterface
{    
    /**
     * Add post is current data on post array
     *
     * @return  array    $postArray     The post array, with appended data
     */
    public function decorate(array $menuItem, MenuConfigInterface $menuConfig): array
    {
        if ($menuItem['id'] == $menuConfig->getPageId()) {
            $menuItem['active'] = true;
        } elseif (\Municipio\Helper\IsCurrentUrl::isCurrentUrl($menuItem['href'])) {
            $menuItem['active'] = true;
        } else {
            $menuItem['active'] = false;
        }

        return $menuItem;
    }
}