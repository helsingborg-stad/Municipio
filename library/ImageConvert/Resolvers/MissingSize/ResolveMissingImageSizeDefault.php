<?php

namespace Municipio\ImageConvert\Resolvers\MissingSize;

use Municipio\ImageConvert\Config\ImageConvertConfig;

class ResolveMissingImageSizeDefault implements ResolveMissingImageSizeInterface
{
    public function getAttachmentDimensions(int $id): array
    {
        return [500,500];
    }
}