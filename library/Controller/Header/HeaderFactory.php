<?php

namespace Municipio\Controller\Header;

use AcfService\AcfService;
use Municipio\Controller\Header\Helper\ExtractMenuItems;
use Municipio\Controller\Header\Helper\HeaderVisibilityClasses;
use Municipio\Controller\Header\MenuItemFactory;
use WpService\WpService;

class HeaderFactory
{
    private ExtractMenuItems $extractMenuItems;
    private HeaderVisibilityClasses $HeaderVisibilityClasses;
    private MenuItemFactory $menuItemFactory;

    public function __construct(
        private WpService $wpService,
        private AcfService $acfService,
        private object $customizer
    ) {
        $this->extractMenuItems = new ExtractMenuItems($this->customizer);
        $this->HeaderVisibilityClasses = new HeaderVisibilityClasses();
        $this->menuItemFactory = new MenuItemFactory();
    }

    public function create(string $id): Header
    {
        return new Header(
            $id,
            $this->wpService,
            $this->acfService,
            $this->menuItemFactory,
            $this->extractMenuItems,
            $this->HeaderVisibilityClasses
        );
    }
}