<?php

namespace Municipio\BackdropBanner\Row\Render;

use ComponentLibrary\Renderer\RendererInterface;
use Municipio\BackdropBanner\BlockRendererInterface;

class BlockRenderer implements BlockRendererInterface
{
    public function __construct(
        private RendererInterface $bladeRenderer,
    ) {}

    public function render(array $attributes): string
    {
        return $this->bladeRenderer->render('backdrop-banner-row', $attributes);
    }

    public static function getViewPathsDir(): array
    {
        return [__DIR__ . '/views/'];
    }
}
