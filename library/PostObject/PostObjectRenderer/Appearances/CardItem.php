<?php

namespace Municipio\PostObject\PostObjectRenderer\Appearances;

use Municipio\PostObject\PostObjectRenderer\PostObjectRendererInterface;

/**
 * CardItem appearance.
 */
class CardItem extends PostObjectBladeRenderer implements PostObjectRendererInterface
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
