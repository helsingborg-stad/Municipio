<?php

namespace Municipio\Controller\Header;

use AcfService\AcfService;
use Municipio\Controller\Header\Helper\ExtractMenuItems;
use WpService\WpService;

class Header
{
    public function __construct(
        private string $id,
        private WpService $wpService,
        private AcfService $acfService,
        private ExtractMenuItems $extractMenuItems
    ) {
    }

    public function getMenuItems(): array
    {
        $menuItems = $this->extractMenuItems->get();
        echo '<pre>' . print_r( $menuItems, true ) . '</pre>';die;
        return [];
    }
}