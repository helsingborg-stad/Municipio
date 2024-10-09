<?php

namespace Municipio\Controller\Navigation\Config;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;

class MenuConfig implements MenuConfigInterface
{
    public function __construct(
        private string $identifier = '',
        private string|int $menuName = '',
        private bool $removeSubLevels = false,
        private bool $removeTopLevel = false,
        private bool|int $fallbackToPageTree = false
    ) {
    }

    /**
     * Retrieves the identifier of the menu configuration.
     *
     * @return string The identifier of the menu configuration.
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * Retrieves the menu name.
     * Its used for fetching which allows both name or menu id.
     *
     * @return string|int The menu name or id.
     */
    public function getMenuName(): string|int
    {
        return $this->menuName;
    }

    /**
     * Retrieves the value indicating whether sub-levels should be removed from the menu.
     *
     * @return bool The value indicating whether sub-levels should be removed.
     */
    public function getRemoveSubLevels(): bool
    {
        return $this->removeSubLevels;
    }

    /**
     * Retrieves the value indicating whether the top level menu should be removed.
     *
     * @return bool The value indicating whether the top level menu should be removed.
     */
    public function getRemoveTopLevel(): bool
    {
        return $this->removeTopLevel;
    }

    public function getFallbackToPageTree(): bool|int
    /**
     * Get the value of the fallbackToPageTree property.
     * When this is an int it will get the children for that page id.
     *
     * @return bool The value of the fallbackToPageTree property.
     */
    {
        return $this->fallbackToPageTree;
    }
}