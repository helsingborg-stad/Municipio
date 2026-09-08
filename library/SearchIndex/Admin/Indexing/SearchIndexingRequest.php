<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Admin\Indexing;

use Municipio\ProgressReporter\ProgressReporterInterface;
use WpService\WpService;

/**
 * Handles authenticated admin requests to update the Search Index.
 */
class SearchIndexingRequest
{
    public const ACTION = 'municipio_search_index_build';
    public const NONCE_ACTION = 'municipio_search_index_build';

    /**
     * Create the Search Index admin request controller.
     */
    public function __construct(
        private WpService $wpService,
        private SearchIndexingRunnerInterface $runner,
        private SearchIndexingLockInterface $lock,
        private ProgressReporterInterface $progressReporter,
    ) {}

    /**
     * Register the authenticated WordPress AJAX action.
     */
    public function addHooks(): void
    {
        $this->wpService->addAction('wp_ajax_' . self::ACTION, [$this, 'handleRequest']);
    }

    /**
     * Validate and execute an admin indexing request.
     */
    public function handleRequest(): void
    {
        $this->progressReporter->start();

        if (!$this->wpService->currentUserCan('manage_options')) {
            $this->progressReporter->finish(
                $this->wpService->__('You are not allowed to start search indexing.', 'municipio')
            );
            return;
        }

        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            $this->progressReporter->finish(
                $this->wpService->__('Search indexing must be started with a POST request.', 'municipio')
            );
            return;
        }

        if ($this->wpService->checkAjaxReferer(self::NONCE_ACTION, '_ajax_nonce', false) === false) {
            $this->progressReporter->finish(
                $this->wpService->__('The indexing request could not be verified. Reload the page and try again.', 'municipio')
            );
            return;
        }

        $lockOwner = bin2hex(random_bytes(16));

        if (!$this->lock->acquire($lockOwner)) {
            $this->progressReporter->finish(
                $this->wpService->__('Search indexing is already in progress.', 'municipio')
            );
            return;
        }

        try {
            $indexedCount = $this->runner->run($lockOwner);
            $message = sprintf(
                $this->wpService->_n(
                    'Search indexing complete. Indexed %d item.',
                    'Search indexing complete. Indexed %d items.',
                    $indexedCount,
                    'municipio',
                ),
                $indexedCount,
            );
        } catch (\Throwable $throwable) {
            $message = $this->wpService->__(
                'Search indexing failed. Check the provider configuration and try again.',
                'municipio',
            );
        } finally {
            $this->lock->release($lockOwner);
        }

        $this->progressReporter->finish($message);
    }
}