<?php

namespace Municipio\PostObject\Renderer\PostObjectRenderer;

use Municipio\PostObject\Renderer\PostObjectRenderer\PostObjectRenderer;

/**
 * BoxSlideItem appearance.
 */
class BoxSliderItem extends PostObjectRenderer
{
    /**
     * @inheritDoc
     */
    public function getViewName(): string
    {
        return 'BoxSliderItem';
    }

    /**
     * @inheritDoc
     */
    public function getConfig(): array
    {
        $defaultConfig = [
            'ratio' => null,
        ];

        return array_merge($defaultConfig, $this->config);
    }
}
