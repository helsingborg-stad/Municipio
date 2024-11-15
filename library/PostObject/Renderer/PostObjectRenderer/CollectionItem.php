<?php

namespace Municipio\PostObject\Renderer\PostObjectRenderer;

/**
 * CardItem appearance.
 */
class CollectionItem extends PostObjectRenderer
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
            'displayFeaturedImage' => false
        ];

        if (!empty($this->config['gridColumnClass']) && is_string($this->config['gridColumnClass'])) {
            $this->config['gridColumnClass'] = explode(' ', $this->config['gridColumnClass']);
        }

        return array_merge($defaultConfig, $this->config);
    }
}
