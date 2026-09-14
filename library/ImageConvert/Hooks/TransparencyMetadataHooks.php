<?php

namespace Municipio\ImageConvert\Hooks;

use Municipio\HooksRegistrar\Hookable;
use Municipio\ImageConvert\TransparencyMetadata;
use WpService\WpService;

class TransparencyMetadataHooks implements Hookable
{
    public function __construct(
        private WpService $wpService,
        private TransparencyMetadata $transparencyMetadata,
    ) {}

    public function addHooks(): void
    {
        $this->wpService->addFilter('wp_generate_attachment_metadata', [$this, 'handle'], 25, 3);
    }

    /**
     * Persist transparency metadata for attachments when WordPress generates metadata.
     *
     * @param mixed $metadata
     * @param int $attachmentId
     * @return mixed
     */
    public function handle(mixed $metadata, int $attachmentId): mixed
    {
        if (!is_array($metadata)) {
            return $metadata;
        }

        return $this->transparencyMetadata->add($metadata, $attachmentId);
    }
}
