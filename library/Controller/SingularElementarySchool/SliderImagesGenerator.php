<?php

namespace Municipio\Controller\SingularElementarySchool;

use Municipio\Schema\ElementarySchool;
use Municipio\Schema\ImageObject;
use ComponentLibrary\Integrations\Image\Image as ImageComponentContract;
use Municipio\Integrations\Component\ImageResolver;

class SliderImagesGenerator
{
    public function __construct(private ElementarySchool $elementarySchool)
    {
    }

    public function generate(): mixed
    {
        return $this->getGalleryImages($this->elementarySchool->getProperty('image'));
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

    private function getImageFromImageObject(ImageObject $imageObject): ?ImageComponentContract
    {
        if (empty($imageObject->getProperty('@id'))) {
            return null;
        }

        return ImageComponentContract::factory($imageObject->getProperty('@id'), [760, false], new ImageResolver());
    }
}
