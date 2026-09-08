<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Admin\Indexing;

use Municipio\ProgressReporter\ProgressReporterInterface;
use Municipio\SearchIndex\Config\SearchIndexConfig;
use WpService\WpService;

/**
 * Indexes eligible posts while reporting admin progress.
 */
class SearchIndexingRunner implements SearchIndexingRunnerInterface
{
    /**
     * Create a provider-neutral admin indexing runner.
     */
    public function __construct(
        private WpService $wpService,
        private SearchIndexConfig $config,
        private PostIndexerFactoryInterface $postIndexerFactory,
        private SearchIndexingLockInterface $lock,
        private ProgressReporterInterface $progressReporter,
    ) {}

    /**
     * Index all eligible posts and return the processed count.
     */
    public function run(string $lockOwner): int
    {
        if (!$this->config->isConfigured()) {
            throw new \RuntimeException(
                $this->wpService->__('The search provider must be configured before indexing.', 'municipio')
            );
        }

        $postIds = $this->getIndexablePostIds();
        $total = count($postIds);

        if ($total === 0) {
            $this->progressReporter->setPercentage(100);
            return 0;
        }

        $indexer = $this->postIndexerFactory->create();

        foreach ($postIds as $offset => $postId) {
            $processed = $offset + 1;
            $this->progressReporter->setMessage(sprintf(
                $this->wpService->__('Indexing %1$d/%2$d', 'municipio'),
                $processed,
                $total,
            ));
            $indexer->index($postId);
            $this->progressReporter->setPercentage(($processed / $total) * 100);
            $this->lock->refresh($lockOwner);
        }

        return $total;
    }

    /**
     * Get a stable list of all post IDs eligible for indexing.
     *
     * @return array<int, int>
     */
    private function getIndexablePostIds(): array
    {
        $postTypes = array_values(array_diff($this->wpService->getPostTypes([
            'public' => true,
            'exclude_from_search' => false,
        ]), ['attachment']));
        $postTypes = $this->wpService->applyFilters('Municipio/SearchIndex/IndexablePostTypes', $postTypes);
        $postStatuses = $this->wpService->applyFilters('Municipio/SearchIndex/IndexablePostStatuses', ['publish']);
        $postIds = [];

        foreach ($postTypes as $postType) {
            $postIds = array_merge($postIds, $this->wpService->getPosts([
                'post_type' => $postType,
                'post_status' => $postStatuses,
                'numberposts' => -1,
                'fields' => 'ids',
                'orderby' => 'ID',
                'order' => 'ASC',
                'suppress_filters' => false,
            ]));
        }

        return array_values(array_map('intval', $postIds));
    }
}