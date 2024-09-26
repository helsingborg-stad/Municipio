<?php

namespace Municipio\Controller\Navigation\Config;

use Municipio\Controller\Navigation\Decorators\Default\ComplementDefaultDecorator;
use Municipio\Controller\Navigation\Decorators\PageTreeFallback\ComplementPageTreeDecorator;
use Municipio\Controller\Navigation\Decorators\StructureMenuItemsDecorator;
use Municipio\Controller\Navigation\Decorators\MenuItemsDecoratorInterface;
use Municipio\Controller\Navigation\Cache\RuntimeCache;
use Municipio\Controller\Navigation\Cache\CacheManager;

class MenuConfig implements MenuConfigInterface
{
    public function __construct(
        private ComplementDefaultDecorator $complementDefaultDecoratorInstance,
        private ComplementPageTreeDecorator $complementPageTreeDecoratorInstance,
        private StructureMenuItemsDecorator $structureMenuItemsDecoratorInstance,
        private RuntimeCache $runTimeCache,
        private CacheManager $cacheManager,
        private string $identifier = '',
        private string $menuName = '',
        private ?int $pageId = null,
        private $wpdb = null,
        private bool $fallbackToPageTree = false,
        private bool $includeTopLevel = true,
        private bool $onlyKeepFirstLevel = false,
        private $context = 'municipio'
    ) {
    }

    public function getRuntimeCache(): RuntimeCache
    {
        return $this->runTimeCache;
    }

    public function getCacheManager(): CacheManager
    {
        return $this->cacheManager;
    }

    public function getComplementDefaultDecoratorInstance(): MenuItemsDecoratorInterface
    {
        return $this->complementDefaultDecoratorInstance;
    }

    public function getComplementPageTreeDecoratorInstance(): MenuItemsDecoratorInterface
    {
        return $this->complementPageTreeDecoratorInstance;
    }

    public function getStructureMenuItemsDecoratorInstance(): MenuItemsDecoratorInterface
    {
        return $this->structureMenuItemsDecoratorInstance;
    }
    
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getMenuName(): string
    {
        return $this->menuName;
    }

    public function getPageId(): ?int
    {
        return $this->pageId;
    }

    public function getWpdb()
    {
        return $this->wpdb;
    }

    public function getFallbackToPageTree(): bool
    {
        return $this->fallbackToPageTree;
    }

    public function getIncludeTopLevel(): bool
    {
        return $this->includeTopLevel;
    }

    public function getOnlyKeepFirstLevel(): bool
    {
        return $this->onlyKeepFirstLevel;
    }

    public function getContext(): string
    {
        return $this->context;
    }
}