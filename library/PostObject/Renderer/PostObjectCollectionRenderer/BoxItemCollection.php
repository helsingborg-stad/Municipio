<?php

namespace Municipio\PostObject\Renderer\PostObjectCollectionRenderer;

use Municipio\PostObject\Renderer\PostObjectRenderer\PostObjectRendererType;

/**
 * BoxItemCollection appearance.
 */
class BoxItemCollection extends PostObjectCollectionRenderer
{
    /**
     * @inheritDoc
     */
    public function getViewName(): string
    {
        return 'BoxItemCollection';
    }

    /**
     * @inheritDoc
     */
    public function getPostObjectRendererType(): PostObjectRendererType
    {
        return PostObjectRendererType::BoxItem;
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
