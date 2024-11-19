<?php

namespace Municipio\PostObject\Renderer\PostObjectCollectionRenderer;

use Municipio\PostObject\Renderer\PostObjectRenderer\PostObjectRendererType;

/**
 * NewsItemCollection appearance.
 */
class NewsItemCollection extends PostObjectCollectionRenderer
{
    /**
     * @inheritDoc
     */
    public function getViewName(): string
    {
        return 'NewsItemCollection';
    }

    /**
     * @inheritDoc
     */
    public function getPostObjectRendererType(): PostObjectRendererType
    {
        return PostObjectRendererType::NewsItem;
    }

    /**
     * @inheritDoc
     */
    public function getConfig(): array
    {
        $defaultConfig = [
            'displayReadingTime' => false,
            'gridColumnClass'    => null,
            'showPlaceholder'    => false
        ];

        return array_merge($defaultConfig, $this->config);
    }
}
