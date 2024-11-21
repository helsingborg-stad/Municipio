<?php

namespace Municipio\PostObject\PostObjectRenderer\Appearances;

use Municipio\PostObject\PostObjectRenderer\PostObjectRendererInterface;

/**
 * BoxGridItem appearance.
 */
class BoxGridItem extends PostObjectBladeRenderer implements PostObjectRendererInterface
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
