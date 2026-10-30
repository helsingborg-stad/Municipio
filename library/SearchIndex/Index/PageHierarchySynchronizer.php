<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Index;

use WpService\WpService;

/**
 * Synchronizes indexed records that inherit a page's URL or section.
 */
class PageHierarchySynchronizer
{
    /** @var array<int, array<int, int>> */
    private array $descendantsOfDeletedPages = [];

    public function __construct(private WpService $wpService)
    {
    }

    /**
     * Re-index a changed page's attachments and all published descendants.
     */
    public function reindexRelatedContent(int $postId): void
    {
        $this->reindexAttachments($postId);

        foreach ($this->getPublishedDescendants($postId) as $descendant) {
            $this->wpService->doAction('Municipio/SearchIndex/IndexPostId', $descendant->ID);
            $this->reindexAttachments($descendant->ID);
        }
    }

    /**
     * Remember a page's descendants before WordPress reparents them.
     */
    public function rememberDescendantsOfDeletedPage(int $postId): void
    {
        $post = $this->wpService->getPost($postId);

        if (!$post instanceof \WP_Post || $this->wpService->getPostType($post) !== 'page') {
            return;
        }

        $this->descendantsOfDeletedPages[$postId] = array_map(
            static fn(\WP_Post $descendant): int => $descendant->ID,
            $this->getPublishedDescendants($postId),
        );
    }

    /**
     * Re-index descendants after WordPress has reassigned their parent.
     */
    public function reindexDescendantsOfDeletedPage(int $postId): void
    {
        $descendantIds = $this->descendantsOfDeletedPages[$postId] ?? [];
        unset($this->descendantsOfDeletedPages[$postId]);

        foreach ($descendantIds as $descendantId) {
            $this->wpService->doAction('Municipio/SearchIndex/IndexPostId', $descendantId);
            $this->reindexAttachments($descendantId);
        }
    }

    /**
     * Re-index attachments whose inherited ancestry may have changed.
     */
    private function reindexAttachments(int $postId): void
    {
        $attachments = $this->wpService->getPosts([
            'post_type' => 'attachment',
            'post_status' => 'inherit',
            'post_parent' => $postId,
            'numberposts' => -1,
        ]);

        foreach ($attachments as $attachment) {
            if (!$attachment instanceof \WP_Post) {
                continue;
            }

            $this->wpService->doAction('Municipio/SearchIndex/IndexPostId', $attachment->ID);
        }
    }

    /**
     * @return array<int, \WP_Post>
     */
    private function getPublishedDescendants(int $postId): array
    {
        $pages = $this->wpService->getPages([
            'child_of' => $postId,
            'post_status' => 'publish',
        ]);

        return is_array($pages) ? $pages : [];
    }
}
