<?php

namespace Municipio\Controller\Navigation\Config;

use Municipio\Controller\Navigation\Config\NewMenuConfigInterface;

class NewMenuConfig implements NewMenuConfigInterface
{
    public function __construct(
        private string $identifier = '',
        private string $menuName = '',
        private ?int $pageId = null,
        private string $postType = '',
        private bool $removeSubLevels = false,
        private bool $removeTopLevel = false,
        private bool $fallbackToPageTree = false
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

    public function getRemoveSubLevels(): bool
    {
        return $this->removeSubLevels;
    }

    public function getRemoveTopLevel(): bool
    {
        return $this->removeTopLevel;
    }

    public function getFallbackToPageTree(): bool
    {
        return $this->fallbackToPageTree;
    }

    // TODO: REMOVE
    public function getPageId(): ?int
    {
        return $this->pageId;
    }

    // TODO: REMOVE
    public function getPostType(): string
    {
        return $this->postType;
    }
}