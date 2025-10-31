<?php

namespace Municipio\PostsList\Config\AppearanceConfig;

/*
 * Default appearance config implementation
 */
class DefaultAppearanceConfig implements AppearanceConfigInterface
{
    /**
     * @inheritdoc
     */
    public function getDesign(): PostDesign
    {
        return PostDesign::CARD;
    }

    /**
     * @inheritdoc
     */
    public function shouldDisplayReadingTime(): bool
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function shouldDisplayPlaceholderImage(): bool
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function shouldDisplayFeaturedImage(): bool
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function getTaxonomiesToDisplay(): array
    {
        return [];
    }
}
