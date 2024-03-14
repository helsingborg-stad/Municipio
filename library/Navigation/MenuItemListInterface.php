<?php
namespace Municipio\Navigation;

interface MenuItemListInterface {
    public function hasItems():bool;
    public function getItems():array;
    public function addItem(MenuItem $menuItem);
}