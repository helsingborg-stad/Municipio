<?php

namespace Municipio\Controller\Header;

use AcfService\AcfService;
use Municipio\Controller\Header\Helper\ExtractMenuItems;
use Municipio\Controller\Header\Helper\HeaderAttributes;
use Municipio\Controller\Header\Helper\HeaderClasses;
use Municipio\Controller\Header\Helper\MenuItemClasses;
use Municipio\Controller\Header\Helper\StickyResolver;
use Municipio\Controller\Header\MenuItemFactory;
use WpService\WpService;

class HeaderFactory
{
    private ExtractMenuItems $extractMenuItems;
    private HeaderClasses $headerClasses;
    private MenuItemFactory $menuItemFactory;
    private HeaderAttributes $headerAttributes;
    private StickyResolver $stickyResolver;

    public function __construct(
        private WpService $wpService,
        private AcfService $acfService,
        private object $customizer
    ) {
        // echo '<pre>' . print_r( $this->customizer->headerSortableHiddenStorage, true ) . '</pre>';die;
        $this->extractMenuItems = new ExtractMenuItems($this->customizer);
        $this->stickyResolver = new StickyResolver($this->extractMenuItems, $this->customizer);
        $this->headerClasses = new HeaderClasses();
        $this->menuItemFactory = new MenuItemFactory(new MenuItemClasses());
        $this->headerAttributes = new HeaderAttributes();
    }

    public function create(string $id): Header
    {
        return new Header(
            $id,
            $this->menuItemFactory,
            $this->extractMenuItems,
            $this->headerClasses,
            $this->headerAttributes,
            $this->stickyResolver
        );
    }
}