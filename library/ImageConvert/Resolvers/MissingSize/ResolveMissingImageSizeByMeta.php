<?php

namespace Municipio\ImageConvert\Resolvers\MissingSize;

use Municipio\ImageConvert\Contract\ImageContract;
use Municipio\ImageConvert\Resolvers\MissingSize\ResolveMissingImageSizeInterface;

class ResolveMissingImageSizeByMeta implements ResolveMissingImageSizeInterface
{
    public function __construct(private $wpService, private ?ResolveMissingImageSizeInterface $inner = null)
    {
        $this->inner = $inner ?? new ResolveMissingImageSizeByFile(
            $wpService
        );
    }

    public function getAttachmentDimensions(ImageContract $image): ?array
    {
        // Get attachment metadata
        $metaData = $this->wpService->getAttachmentMetadata(
            $image->getId()
        );

        if (is_array($metaData)) {
            // Filter the metadata to ensure 'width' and 'height' exist and are numeric
            $metaDataDimensions = array_filter(
                $metaData,
                fn($value, $key) => in_array($key, ['width', 'height']) && is_numeric($value),
                ARRAY_FILTER_USE_BOTH
            );

            // Check if both 'width' and 'height' were found and are numeric
            if (count($metaDataDimensions) === 2) {
                return [
                'width'  => (int) $metaData['width'],
                'height' => (int) $metaData['height']
                ];
            }
        }

        // Delegate to the inner resolver if needed
        return $this->inner->getAttachmentDimensions($image);
    }
}
