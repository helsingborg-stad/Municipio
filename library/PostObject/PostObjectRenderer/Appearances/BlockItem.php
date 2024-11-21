<?php

namespace Municipio\PostObject\PostObjectRenderer\Appearances;

use Municipio\PostObject\PostObjectRenderer\PostObjectRendererInterface;

/**
 * BlockItem appearance.
 */
class BlockItem extends PostObjectBladeRenderer implements PostObjectRendererInterface
{
    /**
     * @inheritDoc
     */
    public function getViewName(): string
    {
        return 'BlockItem';
    }

    /**
     * @inheritDoc
     */
    public function getConfig(): array
    {
        $defaultConfig = [
            'gridColumnClass'    => null,
            'format'             => '12:16',
            'displayReadingTime' => false,
            'showPlaceholder'    => false,
        ];

        return array_merge($defaultConfig, $this->config);
    }
}
