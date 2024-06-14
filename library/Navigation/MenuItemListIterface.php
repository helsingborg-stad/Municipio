<?php
namespace Municipio\Navigation;

interface MenuItemListInterface {
    public function getId():int;
    public function getTitle():string;
    public function getHref():string;
    public function getParentId():int|null;
    public function isCurrent():bool;
}