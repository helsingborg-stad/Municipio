<?php

namespace Municipio\Blocks\Header;

use WpService\Contracts\AddAction;
use WpService\Contracts\RegisterBlockType;

class HeaderBlock
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
