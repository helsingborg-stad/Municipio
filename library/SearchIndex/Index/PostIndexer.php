<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Index;

use Municipio\SearchIndex\Index\Record\CreateRecordFromPost;
use Municipio\SearchIndex\Provider\SearchProviderInterface;
use WpService\WpService;

/**
 * Synchronizes public WordPress posts with a search index provider.
 */
class PostIndexer
{
    private const CHUNK_COUNT_META_KEY = '_municipio_search_index_chunk_count';
    private const EXCLUDED_META_KEY = 'exclude_from_search';
    private const MAX_RECORD_SIZE = 9999;

    public function __construct(
        private WpService $wpService,
        private SearchProviderInterface $provider,
    ) {}

    /**
     * Register indexing hooks.
     */
    public function addHooks(): void
    {
        $this->wpService->addAction('save_post', [$this, 'index'], 999);
        $this->wpService->addAction('delete_post', [$this, 'delete'], 999);
        $this->wpService->addAction('wp_trash_post', [$this, 'delete'], 999);
        $this->wpService->addAction('Municipio/SearchIndex/IndexPostId', [$this, 'index'], 999);
        $this->wpService->addAction('Municipio/SearchIndex/DeletePostId', [$this, 'delete'], 999);
    }

    /**
     * Index a post or remove it when it is not eligible.
     */
    public function index(int|\WP_Post $post): void
    {
        $post = $this->wpService->getPost($post);

        if (!$post instanceof \WP_Post) {
            return;
        }

        if (!$this->shouldIndex($post)) {
            $this->delete($post->ID);
            return;
        }

        $previousChunkCount = (int) $this->wpService->getPostMeta($post->ID, self::CHUNK_COUNT_META_KEY, true);
        $record = $this->createRecord($post);
        $records = $this->splitRecord($record);

        try {
            if (count($records) === 1) {
                $this->provider->saveObject($records[0], ['objectIDKey' => 'uuid']);
            } else {
                $this->provider->saveObjects($records, ['objectIDKey' => 'uuid']);
            }
        } catch (\Throwable $e) {
            return;
        }

        $this->deleteStaleChunks($post->ID, $previousChunkCount, count($records));
        $this->wpService->updatePostMeta($post->ID, self::CHUNK_COUNT_META_KEY, count($records));
    }

    /**
     * Delete all records associated with a post.
     */
    public function delete(int $postId): void
    {
        $recordId = $this->createRecordId($postId);
        $chunkCount = (int) $this->wpService->getPostMeta($postId, self::CHUNK_COUNT_META_KEY, true);
        $objectIds = [$recordId];

        for ($chunk = 1; $chunk < $chunkCount; $chunk++) {
            $objectIds[] = $this->createChunkId($recordId, $chunk);
        }

        try {
            $this->provider->deleteObjects($objectIds);
        } catch (\Throwable) {
            return;
        }
        $this->wpService->deletePostMeta($postId, self::CHUNK_COUNT_META_KEY);
    }

    /**
     * Determine whether a post is eligible for indexing.
     */
    private function shouldIndex(\WP_Post $post): bool
    {
        if ((defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) || wp_is_post_revision($post)) {
            return false;
        }

        $indexableStatuses = $this->wpService->applyFilters('Municipio/SearchIndex/IndexablePostStatuses', ['publish']);

        if (!in_array($this->wpService->getPostStatus($post), $indexableStatuses, true)) {
            return false;
        }

        if ($this->wpService->getPostMeta($post->ID, self::EXCLUDED_META_KEY, true)) {
            return false;
        }

        $postType = $this->wpService->getPostType($post);
        $indexablePostTypes = $this->getIndexablePostTypes();

        return in_array($postType, $indexablePostTypes, true)
            && (bool) $this->wpService->applyFilters('Municipio/SearchIndex/ShouldIndex', true, $post->ID);
    }

    /**
     * Build the provider-neutral post record.
     *
     * @return array<string, mixed>
     */
    private function createRecord(\WP_Post $post): array
    {
        return (new CreateRecordFromPost($this->wpService))->createRecordFromPost($post);
    }

    /**
     * Split a record only when the selected provider requires it.
     *
     * @return array<int, array<string, mixed>>
     */
    private function splitRecord(array $record): array
    {
        $recordTooLarge = $this->provider->shouldSplitRecord() && strlen(serialize($record)) >= self::MAX_RECORD_SIZE;
        $recordTooLarge = (bool) $this->wpService->applyFilters('Municipio/SearchIndex/RecordTooLarge', $recordTooLarge, $record);

        if (!$recordTooLarge) {
            return [$record];
        }

        $nonContentSize = strlen(serialize(array_diff_key($record, ['content' => true])));
        $chunkSize = max(1, self::MAX_RECORD_SIZE - $nonContentSize);
        $chunks = [];
        $content = (string) $record['content'];
        for ($offset = 0, $contentLength = strlen($content); $offset < $contentLength;) {
            $chunk = mb_strcut($content, $offset, $chunkSize, 'UTF-8');
            if ($chunk === '') {
                $chunk = mb_substr($content, 0, 1, 'UTF-8');
            }
            $chunks[] = $chunk;
            $offset += strlen($chunk);
        }
        $chunks = $chunks === [] ? [''] : $chunks;

        return array_map(fn(string $content, int $index): array => [
            ...$record,
            'uuid' => $this->createChunkId((string) $record['uuid'], $index),
            'content' => $content,
            'partial_object_distinct_key' => $record['uuid'],
            'partial_object_total_amount' => count($chunks),
        ], $chunks, array_keys($chunks));
    }

    /**
     * Create a stable ID across sites in a multisite installation.
     */
    private function createRecordId(int $postId): string
    {
        $host = parse_url(home_url(), PHP_URL_HOST);
        $site = is_multisite() ? (string) get_current_blog_id() : '0';

        return sprintf('%s-%s-%d', str_replace('.', '-', (string) $host), $site, $postId);
    }

    /**
     * Get public WordPress post types selected for indexing.
     *
     * @return array<int, string>
     */
    private function getIndexablePostTypes(): array
    {
        $postTypes = array_diff($this->wpService->getPostTypes([
            'public' => true,
            'exclude_from_search' => false,
        ]), ['attachment']);

        return $this->wpService->applyFilters('Municipio/SearchIndex/IndexablePostTypes', $postTypes);
    }

    /**
     * Create the identifier for a split record chunk.
     */
    private function createChunkId(string $recordId, int $chunk): string
    {
        return $chunk === 0 ? $recordId : sprintf('%s-part-%d', $recordId, $chunk);
    }

    /**
     * Remove chunks left behind when a record becomes smaller.
     */
    private function deleteStaleChunks(int $postId, int $previousChunkCount, int $currentChunkCount): void
    {
        if ($previousChunkCount <= $currentChunkCount) {
            return;
        }

        $recordId = $this->createRecordId($postId);
        $objectIds = [];
        for ($chunk = $currentChunkCount; $chunk < $previousChunkCount; $chunk++) {
            $objectIds[] = $this->createChunkId($recordId, $chunk);
        }

        try {
            $this->provider->deleteObjects($objectIds);
        } catch (\Throwable) {
        }
    }
}