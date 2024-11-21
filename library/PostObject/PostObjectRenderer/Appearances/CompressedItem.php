<?php

namespace Municipio\PostObject\PostObjectRenderer\Appearances;

use Municipio\PostObject\PostObjectRenderer\PostObjectRendererInterface;

/**
 * CompressedItem appearance.
 */
class CompressedItem extends PostObjectBladeRenderer implements PostObjectRendererInterface
{
    /**
     * @inheritDoc
     */
    public function getViewName(): string
    {
        return 'CompressedItem';
    }

    /**
     * @inheritDoc
     */
    public function getConfig(): array
    {
        $defaultConfig = [
            'gridColumnClass' => '',
        ];

        return array_merge($defaultConfig, $this->config);
    }
}
