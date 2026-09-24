<?php

namespace Municipio\Controller\Header;

use AcfService\AcfService;
use Municipio\Controller\Header\Header;
use Municipio\Controller\Header\Helper\ExtractMenuItems;
use WpService\WpService;

class HeaderFactory
{
    private ExtractMenuItems $extractMenuItems;

    public function __construct(
        private WpService $wpService,
        private AcfService $acfService,
        private object $customizer
    ) {
        $this->extractMenuItems = new ExtractMenuItems($this->customizer);
    }

    public function create(string $id): Header
    {
        return new Header(
            $id,
            $this->wpService,
            $this->acfService,
            $this->extractMenuItems
        );
    }
}