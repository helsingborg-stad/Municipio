<?php

namespace Municipio\PostObject\Renderer\PostObjectCollectionRenderer;

use Municipio\PostObject\Renderer\PostObjectRenderer\PostObjectRendererType;

/**
 * SegmentItemSlider appearance.
 */
class SegmentItemSlider extends PostObjectCollectionRenderer
{
    /**
     * @inheritDoc
     */
    public function getViewName(): string
    {
        return 'SegmentItemSlider';
    }

    /**
     * @inheritDoc
     */
    public function getPostObjectRendererType(): PostObjectRendererType
    {
        return PostObjectRendererType::SegmentSliderItem;
    }

    /**
     * @inheritDoc
     */
    public function getConfig(): array
    {
        $defaultConfig = [
            'gridColumnClass' => [],
            'reverseColumns'  => false,
        ];

        return array_merge($defaultConfig, $this->config);
    }
}
