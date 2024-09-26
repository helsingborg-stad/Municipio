<?php

namespace Municipio\Controller\Navigation;

class MenuConfig implements MenuConfigInterface
{
    public function __construct(
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