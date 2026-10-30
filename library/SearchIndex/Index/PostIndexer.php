<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Index;

use Municipio\SearchIndex\Index\Record\CreateRecordFromPost;
use Municipio\SearchIndex\Provider\SearchProviderInterface;
use WpService\WpService;

/**
 * Synchronizes public WordPress posts with a search index provider.
 *
 * @mago-expect lint:cyclomatic-complexity This coordinates independent indexing, cleanup, and WordPress lifecycle paths.
 * @mago-expect lint:too-many-methods Small methods keep each indexing responsibility independently testable.
 */
class PostIndexer
{
    private const RECORD_IDS_META_KEY = '_municipio_search_index_record_ids';
    private const EXCLUDED_META_KEY = 'exclude_local_search';

    private PageHierarchySynchronizer $pageHierarchySynchronizer;

    public function __construct(
        private WpService $wpService,
        private SearchProviderInterface $provider,
    ) {
        $this->pageHierarchySynchronizer = new PageHierarchySynchronizer($wpService);
    }

    /**
     * Register indexing hooks.
     */
    public function addHooks(): void
    {
        // wp_after_insert_post runs after post meta has been saved and provides
        // the previous post state. The latter lets us update pages whose URLs
        // or top-level section inherit values from an ancestor.
        $this->wpService->addAction('wp_after_insert_post', [$this, 'indexAfterInsert'], 999, 4);
        $this->wpService->addAction('delete_post', [$this, 'delete'], 999);
        $this->wpService->addAction('wp_trash_post', [$this, 'delete'], 999);
        $this->wpService->addAction('before_delete_post', [$this, 'rememberDescendantsOfDeletedPage']);
        $this->wpService->addAction('deleted_post', [$this, 'reindexDescendantsOfDeletedPage']);
        $this->wpService->addAction('Municipio/SearchIndex/IndexPostId', [$this, 'index'], 999);
        $this->wpService->addAction('Municipio/SearchIndex/DeletePostId', [$this, 'delete'], 999);
    }

    /**
     * Index a saved post and synchronize content inheriting from a changed page.
     *
     * @mago-expect lint:no-boolean-flag-parameter The WordPress wp_after_insert_post hook prescribes this signature.
     */
    public function indexAfterInsert(int $postId, \WP_Post $post, bool $update, ?\WP_Post $postBefore): void
    {
        $this->index($post);

        if (!$update || !$postBefore instanceof \WP_Post || !$this->ancestorFieldsChanged($post, $postBefore)) {
            return;
        }

        // Attachments and descendant pages derive their section and/or URL
        // from this page. Trigger the public action so integrations use the
        // same indexing path as ordinary content updates.
        $this->pageHierarchySynchronizer->reindexRelatedContent($postId);
    }

    /**
     * Remember published descendants before WordPress reparents them.
     */
    public function rememberDescendantsOfDeletedPage(int $postId): void
    {
        $this->pageHierarchySynchronizer->rememberDescendantsOfDeletedPage($postId);
    }

    /**
     * Re-index descendants once WordPress has reassigned their parent.
     */
    public function reindexDescendantsOfDeletedPage(int $postId): void
    {
        $this->pageHierarchySynchronizer->reindexDescendantsOfDeletedPage($postId);
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

        $previousIds = $this->getIndexedRecordIds($post->ID);
        $record = $this->createRecord($post);

        try {
            $currentIds = $this->provider->saveObject($record);
        } catch (\Throwable $e) {
            return;
        }

        $this->deleteStaleRecords($previousIds, $currentIds);
        $this->wpService->updatePostMeta($post->ID, self::RECORD_IDS_META_KEY, wp_json_encode($currentIds));
    }

    /**
     * Delete all records associated with a post.
     */
    public function delete(int $postId): void
    {
        $indexedRecordIds = $this->getIndexedRecordIds($postId);
        $objectIds = $indexedRecordIds !== [] ? $indexedRecordIds : [$this->createRecordId($postId)];

        try {
            $this->provider->deleteObjects($objectIds);
        } catch (\Throwable) {
            return;
        }
        $this->wpService->deletePostMeta($postId, self::RECORD_IDS_META_KEY);
    }

    /**
     * Check whether a page change can alter URLs or inherited section titles.
     */
    private function ancestorFieldsChanged(\WP_Post $post, \WP_Post $postBefore): bool
    {
        return $this->wpService->getPostType($post) === 'page'
            && ($post->post_name !== $postBefore->post_name
                || (int) $post->post_parent !== (int) $postBefore->post_parent
                || $post->post_title !== $postBefore->post_title);
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
     * Get the record IDs stored for a post from a previous indexing run.
     *
     * @return array<int, string>
     */
    private function getIndexedRecordIds(int $postId): array
    {
        $storedIds = json_decode((string) $this->wpService->getPostMeta($postId, self::RECORD_IDS_META_KEY, true), true);

        return is_array($storedIds) ? $storedIds : [];
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
     * Remove records left behind by a previous indexing run that are no longer produced.
     *
     * @param array<int, string> $previousIds
     * @param array<int, string> $currentIds
     * @mago-expect lint:no-empty-catch-clause A stale-record cleanup failure must not invalidate the saved replacement record.
     */
    private function deleteStaleRecords(array $previousIds, array $currentIds): void
    {
        $staleIds = array_values(array_diff($previousIds, $currentIds));

        if ($staleIds === []) {
            return;
        }

        try {
            $this->provider->deleteObjects($staleIds);
        } catch (\Throwable) {
        }
    }
}
