<?php

namespace Municipio\PostObject\Renderer\PostObjectRenderer;

/**
 * SegmentGridItem appearance.
 */
class SegmentGridItem extends PostObjectRenderer
{
    /**
     * @inheritDoc
     */
    public function getViewName(): string
    {
        return 'SegmentGridItem';
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
