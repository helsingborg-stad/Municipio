<?php

namespace Municipio\Controller\Header;
use Municipio\Controller\Header\Helper\MenuItemClasses;
use Municipio\Controller\Header\Helper\HeaderBreakpoint;
use Municipio\Controller\Header\Helper\HeaderKey;

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
        $rawMenuItem[HeaderBreakpoint::DESKTOP->value] = $rawMenuItem[HeaderBreakpoint::DESKTOP->value] ?? [];
        $rawMenuItem[HeaderBreakpoint::MOBILE->value] = $rawMenuItem[HeaderBreakpoint::MOBILE->value] ?? [];
        return new MenuItem($id, $rawMenuItem, $this->menuItemClasses);
    } 
}