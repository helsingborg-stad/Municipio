<?php

namespace Municipio\Controller\Header;

use Municipio\Controller\Header\Helper\HeaderClasses;
use Municipio\Controller\Header\Helper\Enums;
use Municipio\Controller\Header\Helper\ExtractMenuItems;
use Municipio\Controller\Header\MenuItemFactory;
use Municipio\Controller\Header\Helper\StickyResolver;
use Municipio\Controller\Header\Helper\HeaderAttributes;

class Header
{
    private ?array $createdMenuItems = null;

    public function __construct(
        private string $id,
        private MenuItemFactory $menuItemFactory,
        private ExtractMenuItems $extractMenuItems,
        private HeaderClasses $headerClasses,
        private HeaderAttributes $headerAttributes,
        private StickyResolver $stickyResolver
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getCssClasses(): array
    {
        $visibilityClasses = $this->headerClasses->buildVisibilityClasses($this->extractMenuItems->getHeaderItems($this->id));

        return array_merge($visibilityClasses);
    }

    public function getAttributes(): array
    {
        $styleAttributes = $this->headerAttributes->buildStyleAttributes($this->getMenuItems());

        return array_merge($styleAttributes);
    }

    public function isEmpty(): bool
    {
        $rawMenuItems = $this->extractMenuItems->getHeaderItems($this->id);
        return 
            empty($rawMenuItems[Enums::HEADER_BREAKPOINT::DESKTOP->value]) && 
            empty($rawMenuItems[Enums::HEADER_BREAKPOINT::MOBILE->value]);
    }

    public function isSticky(): bool
    {
        return $this->stickyResolver->isSticky($this->id);
    }

    public function getMenuItems(): array
    {
        return $this->createdMenuItems ??= $this->createMenuItems();
    }

    private function createMenuItems(): array
    {
        $rawMenuItems = $this->extractMenuItems->getHeaderItems($this->id);
        $structuredRawMenuItems = [];

        foreach ($rawMenuItems as $breakpoint => $items) {
            $i = 0;
            foreach ($items as $id => $config) {
                $config['order'] = $i;
                $structuredRawMenuItems[$id][$breakpoint] = $config;
                $i++;
            }
        }

        $menuItems = [];
        foreach ($structuredRawMenuItems as $id => $item) {
            $menuItems[$id] = $this->menuItemFactory->create($id, $item);
        }

        return $menuItems;
    }
}