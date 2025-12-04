<?php

namespace Municipio\ImageConvert\Resolvers\MissingSize;

use Municipio\ImageConvert\Config\ImageConvertConfig;
use Municipio\ImageConvert\Contract\ImageContract;

class ResolveMissingImageSizeDefault implements ResolveMissingImageSizeInterface
{
    public function getAttachmentDimensions(ImageContract $image): array
    {
        // Create a readable representation of the ImageContract object
        $imageDetails = sprintf(
            'ImageContract Details: [ID: %s, URL: %s]',
            $image->getId(),
            $image->getUrl()
        );

        // Log the error with additional information about the image
        error_log('ResolveMissingImageSizeDefault: Could not resolve image size by file or meta data. Using default size 500x500. ' . $imageDetails);

        return [
            'width'  => 500,
            'height' => 500
        ];
    }
}
