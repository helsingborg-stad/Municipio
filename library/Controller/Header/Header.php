<?php

namespace Municipio\Controller\Header;

use AcfService\AcfService;
use Municipio\Controller\Header\Helper\HeaderVisibilityClasses;
use Municipio\Controller\Header\Helper\Enums;
use Municipio\Controller\Header\Helper\ExtractMenuItems;
use Municipio\Controller\Header\MenuItemFactory;
use Municipio\Controller\Header\Helper\StickyResolver;
use WpService\WpService;

class Header
{
    private ?array $createdMenuItems = null;

    public function __construct(
        private string $id,
        private WpService $wpService,
        private AcfService $acfService,
        private MenuItemFactory $menuItemFactory,
        private ExtractMenuItems $extractMenuItems,
        private HeaderVisibilityClasses $headerVisibilityClasses,
        private StickyResolver $stickyResolver
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getCssClasses(): array
    {
        //TODO: Fix
        // $desktopVisibilityClasses = $this->headerVisibilityClasses->buildVisibilityClasses($this->getMenuItems(), $this->desktopModifiers);
        // $mobileVisibilityClasses = $this->headerVisibilityClasses->buildVisibilityClasses($this->getMenuItems());

        // return array_merge($desktopVisibilityClasses, $mobileVisibilityClasses);
        return [];
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