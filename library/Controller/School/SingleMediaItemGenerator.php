<?php

declare(strict_types=1);

namespace Municipio\Controller\School;

/**
 * Class SingleMediaItemGenerator
 *
 * Returns the media item when the school has exactly one image or video,
 * so that it can be displayed without a slider.
 */
class SingleMediaItemGenerator implements ViewDataGeneratorInterface
{
    /**
     * Constructor
     */
    public function __construct(
        private ViewDataGeneratorInterface $mediaItemsGenerator,
    ) {}

    /**
     * Generate single media item
     *
     * @return mixed
     */
    public function generate(): mixed
    {
        $mediaItems = $this->mediaItemsGenerator->generate();

        if (MediaItemsCounter::count($mediaItems) !== 1) {
            return null;
        }

        return $mediaItems['imageItems'][0] ?? $mediaItems['videoItems'][0] ?? null;
    }
}
