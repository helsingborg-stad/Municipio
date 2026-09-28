<?php

namespace Municipio\Controller\Header;

class MenuItemFactory
{
    public function create(string $id, int $index, array $rawMenuItem): MenuItem
    {
        return new MenuItem($id, $index, $rawMenuItem);
    } 
}