<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Attachment;

use AcfService\Contracts\GetField;

/**
 * Reads attachment MIME types selected for SearchIndex.
 */
class AttachmentConfig
{
    public function __construct(private GetField $acfService) {}

    /**
     * @return array<int, string>
     */
    public function getMimeTypes(): array
    {
        $mimeTypes = $this->acfService->getField('search_index_attachment_mime_types', 'option');
        return is_array($mimeTypes) ? array_values(array_filter($mimeTypes, 'is_string')) : [];
    }

    public function isEnabled(): bool
    {
        return $this->getMimeTypes() !== [];
    }
}