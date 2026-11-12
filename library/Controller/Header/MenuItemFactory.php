<?php

namespace Municipio\Controller\Header;
use Municipio\Controller\Header\Helper\MenuItemClasses;
use Municipio\Controller\Header\Helper\Enums;

class MenuItemFactory
{
    public function __construct(
        private MenuItemClasses $menuItemClasses
    )
    {
    }

    public function create(
        string $id,
        array $rawMenuItem,
    ): MenuItem
    {
        $rawMenuItem[Enums::HEADER_BREAKPOINT::DESKTOP->value] = $rawMenuItem[Enums::HEADER_BREAKPOINT::DESKTOP->value] ?? [];
        $rawMenuItem[Enums::HEADER_BREAKPOINT::MOBILE->value] = $rawMenuItem[Enums::HEADER_BREAKPOINT::MOBILE->value] ?? [];
        return new MenuItem($id, $rawMenuItem, $this->menuItemClasses);
    } 
}