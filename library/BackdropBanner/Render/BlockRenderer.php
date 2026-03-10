<?php

namespace Municipio\BackdropBanner\Render;

use ComponentLibrary\Renderer\RendererInterface;
use WpService\Contracts\__;

class BlockRenderer
{
    public function __construct(
        private $wpService,
        private RendererInterface $bladeRenderer,
    ) {}

    public function render(array $attributes)
    {
        return $this->bladeRenderer->render('backdrop-banner', $attributes);
    }

    public static function getViewPathsDir(): array
    {
        return [__DIR__ . '/views/'];
    }
}
