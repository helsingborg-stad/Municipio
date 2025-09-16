<?php

namespace Municipio\Controller\SingularPreschool;

use Municipio\Schema\Preschool;
use Municipio\Schema\ImageObject;
use ComponentLibrary\Integrations\Image\Image as ImageComponentContract;
use Municipio\Integrations\Component\ImageResolver;

class SliderItemsGenerator
{
    public function __construct(private Preschool $preschool)
    {
    }

    public function generate(): mixed
    {
        return $this->getGalleryImages($this->preschool->getProperty('image'));
    }

    private function getGalleryImages(array|ImageObject|null $image): ?array
    {
        if (!is_array($image)) {
            return null;
        }

        $images = array_map(function ($item) {
            return is_a($item, ImageObject::class)
                ? $this->getImageFromImageObject($item)
                : null;
        }, array_slice($image, 1)); // Skip the first image as it's used as the main image

        return array_filter($images) ?: null;
    }

    private function getImageFromImageObject(ImageObject $imageObject): ?array
    {
        if (empty($imageObject->getProperty('@id'))) {
            return null;
        }

        return [
            'image'          => ImageComponentContract::factory($imageObject->getProperty('@id'), [760, false], new ImageResolver()),
            'text'           => $imageObject->getProperty('caption') ?: null,
            'layout'         => 'bottom',
            'containerColor' => 'darkest'
        ];
    }
}
