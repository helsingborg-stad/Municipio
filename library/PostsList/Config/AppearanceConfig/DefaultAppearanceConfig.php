<?php

namespace Municipio\PostsList\Config\AppearanceConfig;

class DefaultAppearanceConfig implements AppearanceConfigInterface
{
    public function getDesign(): PostDesign
    {
        return PostDesign::CARD;
    }

    public function shouldDisplayReadingTime(): bool
    {
        return false;
    }

    public function shouldDisplayPlaceholderImage(): bool
    {
        return false;
    }

    public function shouldDisplayFeaturedImage(): bool
    {
        return false;
    }
}
