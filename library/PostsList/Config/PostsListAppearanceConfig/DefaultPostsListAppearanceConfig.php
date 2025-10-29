<?php

namespace Municipio\PostsList\Config\PostsListAppearanceConfig;

class DefaultPostsListAppearanceConfig implements PostsListAppearanceConfigInterface
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
}
