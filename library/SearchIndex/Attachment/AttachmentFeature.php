<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Attachment;

use WpService\WpService;

/**
 * Adds selected media-library attachments to SearchIndex.
 */
class AttachmentFeature
{
    public function __construct(
        private WpService $wpService,
        private AttachmentConfig $config,
    ) {}

    public function addHooks(): void
    {
        $this->wpService->addFilter('Municipio/SearchIndex/IndexablePostTypes', [$this, 'addAttachmentPostType']);
        $this->wpService->addFilter('Municipio/SearchIndex/IndexablePostStatuses', [$this, 'addAttachmentPostStatus']);
        $this->wpService->addFilter('Municipio/SearchIndex/ShouldIndex', [$this, 'shouldIndexAttachment'], 10, 2);
        $this->wpService->addFilter('Municipio/SearchIndex/Record', [$this, 'addAttachmentDetails'], 10, 2);
        $this->wpService->addAction('add_attachment', [$this, 'indexAttachment']);
        $this->wpService->addAction('attachment_updated', [$this, 'indexAttachment']);
        $this->wpService->addAction('delete_attachment', [$this, 'deleteAttachment']);
    }

    /**
     * @param array<int, string> $postTypes
     * @return array<int, string>
     */
    public function addAttachmentPostType(array $postTypes): array
    {
        if (!$this->config->isEnabled()) {
            return $postTypes;
        }

        return in_array('attachment', $postTypes, true) ? $postTypes : [...$postTypes, 'attachment'];
    }

    /**
     * @param array<int, string> $postStatuses
     * @return array<int, string>
     */
    public function addAttachmentPostStatus(array $postStatuses): array
    {
        if (!$this->config->isEnabled()) {
            return $postStatuses;
        }

        return in_array('inherit', $postStatuses, true) ? $postStatuses : [...$postStatuses, 'inherit'];
    }

    public function shouldIndexAttachment(bool $shouldIndex, int $postId): bool
    {
        if ($this->wpService->getPostType($postId) !== 'attachment') {
            return $shouldIndex;
        }

        $mimeType = $this->wpService->getPostMimeType($postId);
        return $shouldIndex && is_string($mimeType) && in_array($mimeType, $this->config->getMimeTypes(), true);
    }

    /**
     * @param array<string, mixed> $record
     * @return array<string, mixed>
     */
    public function addAttachmentDetails(array $record, int $postId): array
    {
        if (($record['post_type'] ?? null) !== 'attachment') {
            return $record;
        }

        $attachmentUrl = $this->wpService->wpGetAttachmentUrl($postId);
        $record['permalink'] = is_string($attachmentUrl) ? $attachmentUrl : '';
        return $record;
    }

    public function indexAttachment(int $postId): void
    {
        $this->wpService->doAction('Municipio/SearchIndex/IndexPostId', $postId);
    }

    public function deleteAttachment(int $postId): void
    {
        $this->wpService->doAction('Municipio/SearchIndex/DeletePostId', $postId);
    }
}