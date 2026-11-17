<?php

namespace Municipio\Controller\SingularEvent\Mappers;

use ComponentLibrary\Integrations\Image\ImageResolverInterface;
use Municipio\Schema\BaseType;
use Municipio\Schema\ImageObject;

/**
 * Maps the images of an event to attributes for the gallery component.
 *
 * The image used as featured image is left out, since it is already shown
 * at the top of the page.
 */
class MapGallery implements EventDataMapperInterface
{
    private const THUMBNAIL_SIZE = [450, 280];
    private const LARGE_SIZE     = [768, 432];

    /**
     * Constructor
     *
     * @param ImageResolverInterface $imageResolver   Resolves image urls.
     * @param int|null               $featuredImageId Id of the featured image, left out of the gallery.
     */
    public function __construct(
        private ImageResolverInterface $imageResolver,
        private ?int $featuredImageId = null,
    ) {
    }

    /**
     * Maps the event images to gallery component attributes.
     *
     * @param BaseType $event
     * @return array|null Gallery component attributes, or null if there are no images to show.
     */
    public function map(BaseType $event): ?array
    {
        $images = $event->getProperty('image');
        $images = is_array($images) ? $images : [$images];

        $list = array_values(array_filter(array_map([$this, 'mapImage'], $images)));

        return empty($list) ? null : ['list' => $list];
    }

    /**
     * Maps a single image to gallery item attributes.
     */
    private function mapImage(mixed $image): ?array
    {
        if (!$image instanceof ImageObject) {
            return null;
        }

        $imageId = (int) $image->getProperty('@id');

        if ($imageId <= 0 || $imageId === $this->featuredImageId) {
            return null;
        }

        $largeImage = $this->imageResolver->getImageUrl($imageId, self::LARGE_SIZE);

        if (empty($largeImage)) {
            return null;
        }

        return [
            'largeImage' => $largeImage,
            'smallImage' => $this->imageResolver->getImageUrl($imageId, self::THUMBNAIL_SIZE),
            'alt'        => $image->getProperty('description') ?: '',
            'caption'    => null,
        ];
    }
}
