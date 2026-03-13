<?php

namespace Municipio\BackdropBanner\Render;

use ComponentLibrary\Renderer\RendererInterface;
use WpService\Contracts\GetBlockWrapperAttributes;

class BlockRenderer
{
    public function __construct(
        private GetBlockWrapperAttributes $wpService,
        private RendererInterface $bladeRenderer,
    ) {}

    public function render(array $attributes): string
    {
        $attributes['startImage'] = $this->getStartImage($attributes);

        return sprintf(
            '<div %s>%s</div>',
            $this->wpService->getBlockWrapperAttributes(),
            $this->bladeRenderer->render('backdrop-banner', $attributes),
        );
    }

    private function getStartImage(array $attributes): ?string
    {
        if (empty($attributes['rows']) || !is_array($attributes['rows'])) {
            return null;
        }

        foreach ($attributes['rows'] as $row) {
            if (!empty($row['imageUrl'])) {
                return $row['imageUrl'];
            }
        }

        return null;
    }

    public static function getViewPathsDir(): array
    {
        return [__DIR__ . '/views/'];
    }
}
