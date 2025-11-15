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
    public function getPlaceholderImageUrl(): ?string
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function getImageRatio(): ImageRatio
    {
        return ImageRatio::SQUARE;
    }

    /**
     * @inheritdoc
     */
    public function getTaxonomiesToDisplay(): array
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function getPostPropertiesToDisplay(): array
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function getNumberOfColumns(): int
    {
        return 1;
    }
}
