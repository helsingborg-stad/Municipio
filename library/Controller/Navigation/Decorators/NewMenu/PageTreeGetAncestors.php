<?php

namespace Municipio\Controller\Navigation\Decorators\NewMenu;

use Municipio\Controller\Navigation\Config\NewMenuConfigInterface;
use Municipio\Controller\Navigation\Helper\GetAncestors;
use Municipio\Controller\Navigation\NewMenuInterface;
use WpService\Contracts\ApplyFilters;

class PageTreeGetAncestors implements NewMenuInterface
{
    public function __construct(private NewMenuInterface $inner, private ApplyFilters $wpService)
    {
    }

    public function getMenuItems(): array
    {
        $menuItems = $this->inner->getMenuItems();

        if (is_int($this->getConfig()->getFallbackToPageTree())) {
            $menuItems = [$this->getConfig()->getFallbackToPageTree()];
        } else {
            $menuItems = GetAncestors::getAncestors();
        }

        return $menuItems;
    }

    public function getConfig(): NewMenuConfigInterface
    {
        return $this->inner->getConfig();
    }
}