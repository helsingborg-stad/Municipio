<?php

namespace Municipio\PostObject\Renderer\PostObjectRenderer;

/**
 * SchemaProjectItem appearance.
 */
class SchemaProjectItem extends PostObjectRenderer
{
    /**
     * @inheritDoc
     */
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
