<?php

namespace Municipio\PostObject\Renderer\PostObjectRenderer;

/**
 * BlockItem appearance.
 */
class BlockItem extends PostObjectRenderer
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
