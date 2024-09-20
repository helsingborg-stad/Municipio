<?php

namespace Municipio\Controller\Navigation\Decorators\MenuItem;

class AppendIsCurrentPostDecorator implements MenuItemDecoratorInterface
{
    public function __construct(
        private int $postId
    ) {}
    
    /**
     * Add post is current data on post array
     *
     * @return  array    $postArray     The post array, with appended data
     */
    public function decorate(array $menuItem): array
    {
        if ($menuItem['ID'] == $this->postId) {
            $menuItem['active'] = true;
        } elseif (\Municipio\Helper\IsCurrentUrl::isCurrentUrl($menuItem['href'])) {
            $menuItem['active'] = true;
        } else {
            $menuItem['active'] = false;
        }

        return $menuItem;
    }
}