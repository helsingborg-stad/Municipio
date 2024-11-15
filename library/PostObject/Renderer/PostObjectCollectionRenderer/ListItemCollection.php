<?php

namespace Municipio\PostObject\Renderer\PostObjectCollectionRenderer;

use Municipio\PostObject\Renderer\PostObjectRenderer\PostObjectRendererType;

/**
 * ListItemCollection appearance.
 */
class ListItemCollection extends PostObjectCollectionRenderer
{
    /**
     * @inheritDoc
     */
    public function getViewName(): string
    {
        return 'ListItemCollection';
    }

    /**
     * @inheritDoc
     */
    public function getPostObjectRendererType(): PostObjectRendererType
    {
        return PostObjectRendererType::ListItem;
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
