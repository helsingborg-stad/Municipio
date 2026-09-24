<?php

namespace Municipio\Controller\HeaderFactory;

use AcfService\AcfService;
use Municipio\Controller\Header\Header;
use WpService\WpService;

class HeaderFactory
{
    public function __construct(
        private WpService $wpService,
        private AcfService $acfService
    ) {}

    public function create(string $id): Header
    {
        return Header::create($id, );
    }
}