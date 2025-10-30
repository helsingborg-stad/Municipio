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
        private AppearanceConfigInterface $innerConfig
    ) {
    }

    /**
     * Indicates if placeholder image should be displayed.
     * @return bool
     */
    public function shouldDisplayPlaceholderImage(): bool
    {
        return $this->shouldDisplayPlaceholderImage;
    }

    /**
     * Get the post design config.
     * @return PostDesign
     */
    public function getDesign(): PostDesign
    {
        return $this->innerConfig->getDesign();
    }

    /**
     * Indicates if reading time should be displayed.
     * @return bool
     */
    public function shouldDisplayReadingTime(): bool
    {
        return $this->innerConfig->shouldDisplayReadingTime();
    }

    public function shouldDisplayFeaturedImage(): bool
    {
        return $this->innerConfig->shouldDisplayFeaturedImage();
    }
}
