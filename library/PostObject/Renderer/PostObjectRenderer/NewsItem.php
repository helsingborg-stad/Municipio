<?php

namespace Municipio\PostObject\Renderer\PostObjectRenderer;

/**
 * NewsItem appearance.
 */
class NewsItem extends PostObjectRenderer
{
    /**
     * @inheritDoc
     */
    public function getViewName(): string
    {
        return 'NewsItem';
    }

    /**
     * @inheritDoc
     */
    public function getConfig(): array
    {
        $defaultConfig = [
            'gridColumnClass' => null
        ];

        return array_merge($defaultConfig, $this->config);
    }
}
