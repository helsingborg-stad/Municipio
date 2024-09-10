<?php

namespace Municipio\ImageConvert\Resolvers\MissingSize;

interface ResolveMissingImageSizeInterface
{
    /**
     * Get attachment dimensions based on the specific resolver.
     *
     * @param int $id The attachment ID.
     * @return array|null Attachment dimensions or null if not found.
     */
    public function getAttachmentDimensions(int $id): ?array;
}