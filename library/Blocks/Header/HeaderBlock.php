<?php

namespace Municipio\Blocks\Header;

use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\AddAction;
use WpService\Contracts\AddFilter;
use WpService\Contracts\RegisterBlockType;

class HeaderBlock implements Hookable
{
    public function __construct(
        private RegisterBlockType&AddAction&AddFilter $wpService,
    ) {}

    public function addHooks(): void
    {
        $this->wpService->addAction('init', function () {
            $this->wpService->registerBlockType(__DIR__);
        });

        $this->wpService->addFilter('Municipio/viewPaths', [$this, 'addViewPath']);
    }

    public function addViewPath(array $paths): array
    {
        return [...$paths, __DIR__ . '/views'];
    }
}
