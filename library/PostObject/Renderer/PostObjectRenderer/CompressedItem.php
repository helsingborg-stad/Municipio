<?php

namespace Municipio\PostObject\Renderer\PostObjectRenderer;

/**
 * CompressedItem appearance.
 */
class CompressedItem extends PostObjectRenderer
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
