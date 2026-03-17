<?php

namespace Municipio\BackdropBanner\Row\Render;

use ComponentLibrary\Renderer\RendererInterface;
use Municipio\BackdropBanner\BlockRendererInterface;
use WpService\Contracts\GetBlockWrapperAttributes;

class BlockRenderer implements BlockRendererInterface
{
    public function __construct(
        private GetBlockWrapperAttributes $wpService,
        private RendererInterface $bladeRenderer,
    ) {}

    public function render(array $attributes): string
    {
        return sprintf(
            '<div %s>%s</div>',
            $this->wpService->getBlockWrapperAttributes(),
            $this->bladeRenderer->render('backdrop-banner-row', $attributes),
        );
    }

    public static function getViewPathsDir(): array
    {
        return [__DIR__ . '/views/'];
    }
}
