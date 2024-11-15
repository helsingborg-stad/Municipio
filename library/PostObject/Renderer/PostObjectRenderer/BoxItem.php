<?php

namespace Municipio\PostObject\Renderer\PostObjectRenderer;

use Municipio\PostObject\Renderer\PostObjectRenderer\PostObjectRenderer;

/**
 * BoxItem appearance.
 */
class BoxItem extends PostObjectRenderer
{
    /**
     * @inheritDoc
     */
    public function getViewName(): string
    {
        return 'BoxItem';
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
