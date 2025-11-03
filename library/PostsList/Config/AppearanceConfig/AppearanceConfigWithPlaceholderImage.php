<?php

namespace Municipio\PostsList\Config\AppearanceConfig;

use Municipio\PostsList\Config\AppearanceConfig\AppearanceConfigInterface;
use Municipio\PostsList\Config\AppearanceConfig\PostDesign;

/**
 * Decorator for AppearanceConfigInterface to add placeholder image logic.
 */
class AppearanceConfigWithPlaceholderImage implements AppearanceConfigInterface
{
    /**
     * @param bool $shouldDisplayPlaceholderImage
     * @param AppearanceConfigInterface $innerConfig
     */
    public function __construct(
        private bool $shouldDisplayPlaceholderImage,
        private AppearanceConfigInterface $innerConfig = new DefaultAppearanceConfig()
    ) {
    }

    /**
     * @inheritDoc
     */
    public function shouldDisplayPlaceholderImage(): bool
    {
        return $this->shouldDisplayPlaceholderImage;
    }

    /**
     * @inheritDoc
     * @codeCoverageIgnore
     */
    public function getDesign(): PostDesign
    {
        return $this->innerConfig->getDesign();
    }

    /**
     * @inheritDoc
     * @codeCoverageIgnore
     */
    public function shouldDisplayReadingTime(): bool
    {
        return $this->innerConfig->shouldDisplayReadingTime();
    }

    /**
     * @inheritDoc
     * @codeCoverageIgnore
     */
    public function shouldDisplayFeaturedImage(): bool
    {
        return $this->innerConfig->shouldDisplayFeaturedImage();
    }

    /**
     * @inheritDoc
     * @codeCoverageIgnore
     */
    public function getTaxonomiesToDisplay(): array
    {
        return $this->innerConfig->getTaxonomiesToDisplay();
    }

    /**
     * @inheritDoc
     * @codeCoverageIgnore
     */
    public function getNumberOfColumns(): int
    {
        return $this->innerConfig->getNumberOfColumns();
    }
}
