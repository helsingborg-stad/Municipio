<?php

namespace Municipio\ImageConvert;

use Municipio\ImageConvert\Config\ImageConvertConfig;

class ResolveMissingImageSizeDefault implements ResolveMissingImageSizeInterface
{
    public function getAttachmentDimensions(int $id): array
    {
        return [500,500];
    }
}