<?php

namespace Municipio\ImageConvert;

use WpService\WpService;

class TransparencyMetadata
{
    public const ATTACHMENT_METADATA_KEY = 'municipioHasTransparency';

    public function __construct(
        private WpService $wpService,
        private TransparencyDetector $transparencyDetector,
    ) {}

    /**
     * Add transparency information to attachment metadata.
     *
     * @param array $metadata
     * @param int $attachmentId
     * @return array
     */
    public function add(array $metadata, int $attachmentId): array
    {
        $mimeType = $this->wpService->getPostMimeType($attachmentId);
        if (!is_string($mimeType) || strpos($mimeType, 'image/') !== 0) {
            return $metadata;
        }

        $filePath = $this->wpService->getAttachedFile($attachmentId);
        if (!is_string($filePath) || $filePath === '') {
            return $metadata;
        }

        $metadata[self::ATTACHMENT_METADATA_KEY] = $this->transparencyDetector->hasTransparency($filePath, $mimeType);

        return $metadata;
    }
}
