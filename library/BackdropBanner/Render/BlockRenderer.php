<?php

namespace Municipio\BackdropBanner\Render;

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
        $attributes['startImage'] = $this->getStartImage($attributes);
        $attributes['startImagePosition'] = $this->getStartImagePosition($attributes);
        // echo '<pre>' . print_r( $attributes, true ) . '</pre>';die;
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

    private function getStartImagePosition(array $attributes): string
    {
        if (empty($attributes['rows']) || !is_array($attributes['rows'])) {
            return '50% 50%';
        }

        foreach ($attributes['rows'] as $row) {
            if (empty($row['imageUrl'])) {
                continue;
            }

            $focalPointX = (float) ($row['focalPointX'] ?? 0.5);
            $focalPointY = (float) ($row['focalPointY'] ?? 0.5);

            return sprintf('%s%% %s%%', $focalPointX * 100, $focalPointY * 100);
        }

        return '50% 50%';
    }

    public static function getViewPathsDir(): array
    {
        return [__DIR__ . '/views/'];
    }
}
