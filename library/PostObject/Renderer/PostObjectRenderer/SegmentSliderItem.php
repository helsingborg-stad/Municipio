<?php

namespace Municipio\PostObject\Renderer\PostObjectRenderer;

/**
 * SegmentSliderItem appearance.
 */
class SegmentSliderItem extends PostObjectRenderer
{
    /**
     * @inheritDoc
     */
    public function getViewName(): string
    {
        return 'SegmentSliderItem';
    }

    /**
     * @inheritDoc
     */
    public function getConfig(): array
    {
        $defaultConfig = [
            'reverseColumns' => false,
        ];

        return array_merge($defaultConfig, $this->config);
    }
}
