<?php

namespace Municipio\Controller\Navigation\Config;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;

class MenuConfig implements MenuConfigInterface
{
    public function __construct(
        private string $identifier = '',
        private string $menuName = '',
        private bool $removeSubLevels = false,
        private bool $removeTopLevel = false,
        private bool|int $fallbackToPageTree = false
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

    public function getFallbackToPageTree(): bool|int
    {
        return $this->fallbackToPageTree;
    }
}