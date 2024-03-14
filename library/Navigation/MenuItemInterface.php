<?php
namespace Municipio\Navigation;

interface MenuItemInterface {
    public function getId():int;
    public function getTitle():string;
    public function getHref():string;
    public function getParentId():int|null;
    public function isCurrent():bool;
}