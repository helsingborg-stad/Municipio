<?php

namespace Municipio\PostsList\Config\AppearanceConfig;

interface AppearanceConfigInterface
{
    /**
     * Get design for the posts list
     *
     * @return PostDesign
     */
    public function getDesign(): PostDesign;

    public function shouldDisplayReadingTime(): bool;

    public function shouldDisplayPlaceholderImage(): bool;
}
