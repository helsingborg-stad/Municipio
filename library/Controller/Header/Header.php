<?php

namespace Municipio\Controller\Header;

use AcfService\AcfService;
use Municipio\Controller\Header\Helper\HeaderVisibilityClasses;
use Municipio\Controller\Header\Helper\ExtractMenuItems;
use Municipio\Controller\Header\MenuItemFactory;
use WpService\WpService;

class Header
{
    public function __construct(
        private string $id,
        private WpService $wpService,
        private AcfService $acfService,
        private MenuItemFactory $menuItemFactory,
        private ExtractMenuItems $extractMenuItems,
        private HeaderVisibilityClasses $HeaderVisibilityClasses
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getCssClasses(): array
    {
        $desktopVisibilityClasses = $this->HeaderVisibilityClasses->getVisibilityClasses($this->getDesktopMenuItems(), ['@lg', '@xl']);
        $mobileVisibilityClasses = $this->HeaderVisibilityClasses->getVisibilityClasses($this->getMobileMenuItems());

        return array_merge($desktopVisibilityClasses, $mobileVisibilityClasses);
    }

    public function getMenuItems(): array
    {
        return [
            'desktop' => $this->getDesktopMenuItems(),
            'mobile' => $this->getMobileMenuItems(),
        ];
    }

    private function getDesktopMenuItems(): array
    {
        $rawMenuItems = $this->extractMenuItems->getHeaderItems($this->id)['desktop'];
        // echo '<pre>' . print_r( $rawMenuItems, true ) . '</pre>';die;
        $menuItems = [];

        $i = 0;
        foreach ($rawMenuItems as $id => $rawMenuItem) {
            $menuItems[] = $this->menuItemFactory->create($id, $i, $rawMenuItem);
            $i++;
        }

        return $menuItems;
    }

    private function getMobileMenuItems(): array
    {
        $menuItems = $this->extractMenuItems->getHeaderItems($this->id);
        return $menuItems['mobile'];
    }
}