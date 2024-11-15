<?php

namespace Municipio\PostObject\Renderer\PostObjectRenderer;

/**
 * ListItem appearance.
 */
class ListItem extends PostObjectRenderer
{
    /**
     * @inheritDoc
     */
    public function getViewName(): string
    {
        return 'ListItem';
    }

    /**
     * @inheritDoc
     */
    public function getConfig(): array
    {
        $defaultConfig = [];
        return array_merge($defaultConfig, $this->config);
    }
}
