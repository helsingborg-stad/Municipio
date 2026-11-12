<?php

declare(strict_types=1);

namespace Municipio\Controller\School;

/**
 * Class SliderItemsGenerator
 *
 * Returns media items for the slider. A slider is only generated when there
 * is more than one media item, a single item is handled by SingleMediaItemGenerator.
 */
class SliderItemsGenerator implements ViewDataGeneratorInterface
{
    /**
     * Constructor
     */
    public function __construct(
        private ViewDataGeneratorInterface $mediaItemsGenerator,
    ) {}

    /**
     * Generate slider items
     *
     * @return mixed
     */
    public function generate(): mixed
    {
        $mediaItems = $this->mediaItemsGenerator->generate();

        if (MediaItemsCounter::count($mediaItems) <= 1) {
            return [];
        }

        return $mediaItems;
    }
}
