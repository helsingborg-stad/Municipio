<?php

namespace Municipio\ImageConvert\Resolvers\MissingSize;

use Municipio\ImageConvert\Contract\ImageContract;

interface ResolveMissingImageSizeInterface
{
    /**
     * Get attachment dimensions based on the specific resolver.
     *
     * @param int $image A object of type ImageContract.
     * @return array|null Attachment dimensions or null if not found.
     */
    public function getAttachmentDimensions(ImageContract $image): ?array;
}
