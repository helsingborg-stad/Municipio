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

    /**
     * Should display reading time
     *
     * @return bool
     */
    public function shouldDisplayReadingTime(): bool;

    /**
     * Should display placeholder image
     *
     * @return bool
     */
    public function shouldDisplayPlaceholderImage(): bool;

    /**
     * Should display featured image
     *
     * @return bool
     */
    public function shouldDisplayFeaturedImage(): bool;

    /**
     * Get taxonomies to display tags from
     *
     * @return string[] Taxonomy slugs
     */
    public function getTaxonomiesToDisplay(): array;
}
