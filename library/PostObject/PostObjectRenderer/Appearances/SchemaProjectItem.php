<?php

namespace Municipio\PostObject\PostObjectRenderer\Appearances;

use Municipio\PostObject\PostObjectRenderer\PostObjectRendererInterface;

class SchemaProjectItem extends PostObjectBladeRenderer implements PostObjectRendererInterface
{
    public function getViewName(): string
    {
        return 'SchemaProjectItem';
    }

    /**
     * @inheritDoc
     */
    public function getConfig(): array
    {
        $defaultConfig = [
            'gridColumnClass' => null,
            'showPlaceholder' => false
        ];

        return array_merge($defaultConfig, $this->config);
    }
}
