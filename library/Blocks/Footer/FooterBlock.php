<?php

namespace Municipio\Blocks\Footer;

use WpService\Contracts\AddAction;
use WpService\Contracts\RegisterBlockType;

class FooterBlock
{
    public function __construct(
        private RegisterBlockType&AddAction $wpService,
    ) {}

    public function register(): void
    {
        $this->wpService->addAction('init', function () {
            $this->wpService->registerBlockType(__DIR__);
        });
    }
}
