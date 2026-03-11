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
        $attributes['startImage'] = $this->getStartImage($attributes);
        // echo '<pre>' . print_r($attributes, true) . '</pre>';
        return $this->bladeRenderer->render('backdrop-banner', $attributes);
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
