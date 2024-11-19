<?php

namespace Municipio\PostObject\Renderer\PostObjectRenderer;

use Municipio\PostObject\Renderer\PostObjectRenderer\PostObjectRenderer;

/**
 * BoxGridItem appearance.
 */
class BoxGridItem extends PostObjectRenderer
{
    /**
     * @inheritDoc
     */
    public function getViewName(): string
    {
        return 'BoxGridItem';
    }

    /**
     * @inheritDoc
     */
    public function getConfig(): array
    {
        $defaultConfig = [
            'gridColumnClass' => null,
            'ratio'           => null,
        ];

        return array_merge($defaultConfig, $this->config);
    }
}
