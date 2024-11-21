<?php

namespace Municipio\PostObject\PostObjectRenderer\Appearances;

use Municipio\PostObject\PostObjectRenderer\PostObjectRendererInterface;

/**
 * SegmentItem appearance.
 */
class SegmentItem extends PostObjectBladeRenderer implements PostObjectRendererInterface
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
