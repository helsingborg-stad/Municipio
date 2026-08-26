<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Index;

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
        } catch (\Throwable) {
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
        $postType = $this->wpService->getPostType($post);
        $postTypeObject = $postType ? $this->wpService->getPostTypeObject($postType) : null;
        $taxonomies = $this->wpService->getPostTaxonomies($post);
        $tags = [];

        foreach ($taxonomies as $taxonomy) {
            if ($taxonomy === 'category') {
                continue;
            }

            $terms = $this->wpService->wpGetPostTerms($post->ID, $taxonomy);
            if (!$terms instanceof \WP_Error) {
                $tags = [...$tags, ...array_map(
                    static fn(\WP_Term $term): string => $term->name,
                    $terms,
                )];
            }
        }

        $categoryTerms = $this->wpService->wpGetPostTerms($post->ID, 'category');
        $categories = $categoryTerms instanceof \WP_Error ? [] : array_map(
            static fn(\WP_Term $term): string => $term->name,
            $categoryTerms,
        );
        $thumbnailId = $this->wpService->getPostThumbnailId($post);
        $thumbnail = $thumbnailId ? get_the_post_thumbnail_url($post, [480, 270]) : '';
        $permalink = $this->wpService->getPostPermalink($post);
        $content = $this->sanitizeText($this->wpService->applyFilters('the_content', $post->post_content));
        $content = $this->wpService->applyFilters('Municipio/SearchIndex/Record/Content', $content, $post->ID);

        $record = [
            'uuid' => $this->createRecordId($post->ID),
            'ID' => (string) $post->ID,
            'post_title' => $this->sanitizeText($this->wpService->applyFilters('the_title', $post->post_title)),
            'post_excerpt' => $this->createExcerpt($post),
            'content' => is_string($content) ? $content : '',
            'permalink' => is_string($permalink) ? $permalink : '',
            'post_date' => strtotime($post->post_date),
            'post_date_formatted' => date((string) $this->wpService->getOption('date_format'), strtotime($post->post_date)),
            'post_modified' => strtotime($post->post_modified),
            'thumbnail' => is_string($thumbnail) ? $thumbnail : '',
            'thumbnail_alt' => $thumbnailId ? (string) $this->wpService->getPostMeta($thumbnailId, '_wp_attachment_image_alt', true) : '',
            'tags' => $tags,
            'categories' => $categories,
            'search_index_timestamp' => current_time('Y-m-d H:i:s'),
            'post_type' => $postType,
            'post_type_name' => $postTypeObject?->labels->name ?? '',
            'top_most_parent' => $this->getTopMostParentTitle($post),
            'origin_site' => get_bloginfo('name'),
            'origin_site_url' => get_bloginfo('url'),
        ];

        if (is_multisite()) {
            $record['blog_id'] = get_current_blog_id();
        }

        return $this->wpService->applyFilters('Municipio/SearchIndex/Record', $record, $post->ID);
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

    /**
     * Build a searchable excerpt from a post.
     */
    private function createExcerpt(\WP_Post $post): string
    {
        $excerpt = get_the_excerpt($post);
        $excerpt = is_string($excerpt) && $excerpt !== '' ? $excerpt : $post->post_content;
        $excerpt = preg_replace('/\[(.*?)\]/', '', $excerpt) ?? '';

        return wp_trim_words($this->sanitizeText($excerpt), 55, '...');
    }

    /**
     * Remove markup and normalize whitespace in searchable text.
     */
    private function sanitizeText(string $content): string
    {
        $content = preg_replace('/<(script|style|noscript)\b[^>]*>.*?<\/\1>/is', '', $content) ?? '';
        return preg_replace('/\s+/', ' ', strip_tags($content)) ?? '';
    }

    /**
     * Resolve the highest parent title for a hierarchical post.
     */
    private function getTopMostParentTitle(\WP_Post $post): string
    {
        $ancestors = $this->wpService->getPostAncestors($post);
        $topMostParentId = $ancestors !== [] ? end($ancestors) : $post->ID;
        $topMostParent = $this->wpService->getPost((int) $topMostParentId);

        return $topMostParent instanceof \WP_Post ? $topMostParent->post_title : '';
    }
}