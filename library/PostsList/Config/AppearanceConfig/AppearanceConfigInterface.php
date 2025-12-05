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
     * Get placeholder image URL
     *
     * @return string|null
     */
    public function getPlaceholderImageUrl(): null|string;

    /**
     * Get image ratio for the posts list
     *
     * @return ImageRatio
     */
    public function getImageRatio(): ImageRatio;

    /**
     * Get taxonomies to display tags from
     *
     * @return string[] Taxonomy slugs
     */
    public function getTaxonomiesToDisplay(): array;

    /**
     * Get post properties to display
     *
     * @return string[] Post property keys
     */
    public function getPostPropertiesToDisplay(): array;

    /**
     * Get number of columns for the posts list
     *
     * @return int
     */
    public function getNumberOfColumns(): int;

    /**
     * Get date source setting
     *
     * @return string
     */
    public function getDateSource(): string;

    /**
     * Get date format
     *
     * @return DateFormat
     */
    public function getDateFormat(): DateFormat;
}
