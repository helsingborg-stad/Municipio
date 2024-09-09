<?php

namespace Municipio\ImageConvert;

class ResolveMissingImageSizeByMeta implements ResolveMissingImageSizeInterface
{
    private $wpService;
    private ?ResolveMissingImageSizeInterface $inner;

    public function __construct($wpService, ?ResolveMissingImageSizeInterface $inner = null)
    {
        $this->wpService = $wpService;
        // Default to ResolveMissingImageSizeByFile if no inner is provided
        $this->inner = $inner ?? new ResolveMissingImageSizeByFile($wpService);
    }

    public function getAttachmentDimensions(int $id): ?array
    {
        $metaData = wp_get_attachment_metadata($id);  // $this->wpService->getAttachmentMetadata($id);
        if (!empty($metaData['width']) && !empty($metaData['height'])) {
            return [
                $metaData['width'],
                $metaData['height']
            ];
        }

        // Delegate to the inner resolver
        return $this->inner->getAttachmentDimensions($id);
    }
}