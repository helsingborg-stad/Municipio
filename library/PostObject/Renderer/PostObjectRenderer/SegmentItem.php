<?php

namespace Municipio\PostObject\Renderer\PostObjectRenderer;

/**
 * SegmentItem appearance.
 */
class SegmentItem extends PostObjectRenderer
{
    /**
     * @inheritDoc
     */
    public function getViewName(): string
    {
        return 'SegmentItem';
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
