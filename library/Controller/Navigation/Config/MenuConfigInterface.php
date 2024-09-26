<?php

namespace Municipio\Controller\Navigation\Config;

use Municipio\Controller\Navigation\Decorators\MenuItemsDecoratorInterface;
use Municipio\Controller\Navigation\Cache\RuntimeCache;
use Municipio\Controller\Navigation\Cache\CacheManager;

interface MenuConfigInterface
{
    public function getComplementDefaultDecoratorInstance(): MenuItemsDecoratorInterface;
    public function getComplementPageTreeDecoratorInstance(): MenuItemsDecoratorInterface;
    public function getStructureMenuItemsDecoratorInstance(): MenuItemsDecoratorInterface;
    public function getRuntimeCache(): RuntimeCache;
    public function getCacheManager(): CacheManager;
    public function getIdentifier(): string;
    public function getMenuName(): string;
    public function getPageId(): ?int;
    public function getWpdb();
    public function getFallbackToPageTree(): bool;
    public function getIncludeTopLevel(): bool;
    public function getOnlyKeepFirstLevel(): bool;
    public function getContext(): string;
}