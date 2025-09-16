<?php

namespace Municipio\Controller\SingularPreschool;

use Municipio\Schema\ImageObject;
use Municipio\Schema\Preschool;
use ComponentLibrary\Integrations\Image\Image as ImageComponentContract;
use Municipio\Integrations\Component\ImageResolver;

class FeaturedImageAttributesGenerator
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
        if (!is_array($image) || !is_a($image[0], ImageObject::class)) {
            return null;
        }

        $imageObject = $image[0];

        return [
            'src'     => ImageComponentContract::factory($imageObject->getProperty('@id'), [760, false], new ImageResolver()),
            'caption' => $imageObject->getProperty('caption') ?: null,
        ];
    }
}
