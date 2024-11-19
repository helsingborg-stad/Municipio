<?php

namespace Municipio\PostObject\Renderer\PostObjectRenderer;

/**
 * CardItem appearance.
 */
class CardItem extends PostObjectRenderer
{
    /**
     * @inheritDoc
     */
    public function getViewName(): string
    {
        return 'CardItem';
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
