<?php

namespace Municipio\Blocks\Footer;

use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\AddAction;
use WpService\Contracts\AddFilter;
use WpService\Contracts\RegisterBlockType;

class FooterBlock implements Hookable
{
    private RegisterBlockType&AddAction&AddFilter $wpService;

    public function __construct(RegisterBlockType&AddAction&AddFilter $wpService)
    {
        $this->wpService = $wpService;
    }

    public function addHooks(): void
    {
        $this->wpService->addAction('init', [$this, 'registerBlockType']);
        $this->wpService->addFilter('Municipio/viewPaths', [$this, 'addViewPathFilter']);
    }

    public function registerBlockType(): void
    {
        $this->wpService->registerBlockType(__DIR__);
    }

    public function addViewPathFilter(array $paths): array
    {
        return [...$paths, __DIR__ . '/views'];
    }
}
