<?php

namespace Municipio\PostObject\PostObjectRenderer\Appearances;

use Municipio\PostObject\PostObjectRenderer\PostObjectRendererInterface;

/**
 * CardItem appearance.
 */
class CollectionItem extends PostObjectBladeRenderer implements PostObjectRendererInterface
{
    /**
     * @inheritDoc
     */
    public function getViewName(): string
    {
        return 'CollectionItem';
    }

    /**
     * @inheritDoc
     */
    public function getConfig(): array
    {
        $defaultConfig = [
            'gridColumnClass'      => [],
            'displayFeaturedImage' => true
        ];

        if (!empty($this->config['gridColumnClass']) && is_string($this->config['gridColumnClass'])) {
            $this->config['gridColumnClass'] = explode(' ', $this->config['gridColumnClass']);
        }

        return array_merge($defaultConfig, $this->config);
    }
}
