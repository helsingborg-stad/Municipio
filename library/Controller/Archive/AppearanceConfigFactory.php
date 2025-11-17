<?php

namespace Municipio\Controller\Archive;

use Municipio\PostsList\Config\AppearanceConfig\AppearanceConfigInterface;
use Municipio\PostsList\Config\AppearanceConfig\PostDesign;

/**
 * Factory class for creating AppearanceConfig instances
 */
class AppearanceConfigFactory
{
    /**
     * Create an AppearanceConfig instance
     *
     * @param array $data
     * @return AppearanceConfigInterface
     */
    public function create(array $data): AppearanceConfigInterface
    {
        $shouldDisplayFeaturedImage = $this->displayFeaturedImage($data['archiveProps']);
        $shouldDisplayReadingTime   = $this->displayReadingTime($data['archiveProps']);
        $template                   = $data['archiveProps']->style ?? 'cards';
        $design                     = match ($template) {
            'cards' => PostDesign::CARD,
            'compressed' => PostDesign::COMPRESSED,
            'collection' => PostDesign::COLLECTION,
            'grid' => PostDesign::BLOCK,
            'newsitem' => PostDesign::NEWSITEM,
            'schema' => PostDesign::SCHEMA,
            'list' => PostDesign::TABLE,
            default => PostDesign::CARD,
        };

        return (new AppearanceConfigBuilder())
            ->setNumberOfColumns($data['archiveProps']->numberOfColumns ?? 1)
            ->setShouldDisplayFeaturedImage($shouldDisplayFeaturedImage)
            ->setShouldDisplayReadingTime($shouldDisplayReadingTime)
            ->setTaxonomiesToDisplay($data['archiveProps']->taxonomiesToDisplay ?? null ?: [])
            ->setPostPropertiesToDisplay($data['archiveProps']->postPropertiesToDisplay ?? [])
            ->setDesign($design)
            ->build();
    }

    /**
     * Display the featured image based on the provided arguments.
     *
     * @param object $args The arguments for displaying the featured image.
     * @return bool Returns true if the featured image should be displayed, false otherwise.
     */
    private function displayFeaturedImage($args)
    {
        if (!is_object($args)) {
            $args = (object) [];
        }

        if (!isset($args->displayFeaturedImage)) {
            return false;
        }

        return (bool) $args->displayFeaturedImage;
    }

    /**
     * Determines whether to display the reading time for an archive.
     *
     * @param array $args The arguments for displaying the reading time.
     * @return bool Returns true if the reading time is set in the arguments, false otherwise.
     */
    private function displayReadingTime($args)
    {
        if (!is_object($args)) {
            $args = (object) [];
        }

        if (!isset($args->readingTime)) {
            return false;
        }

        return (bool) $args->readingTime;
    }
}
