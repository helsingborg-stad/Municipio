<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Admin\Indexing;

use Municipio\ProgressReporter\AjaxAction\AbstractProgressAjaxAction;
use Municipio\ProgressReporter\AjaxAction\ProgressAjaxActionConfig;
use Municipio\ProgressReporter\AjaxAction\ProgressAjaxActionMessages;
use Municipio\ProgressReporter\ProgressReporterInterface;
use WpService\WpService;

/**
 * Handles authenticated admin requests to update the Search Index.
 */
class SearchIndexingRequest extends AbstractProgressAjaxAction
{
    public const ACTION = 'municipio_search_index_build';
    public const NONCE_ACTION = 'municipio_search_index_build';

    /**
     * Create the Search Index admin request controller.
     */
    public function __construct(
        private WpService $searchWpService,
        private SearchIndexingRunnerInterface $runner,
        private SearchIndexingLockInterface $lock,
        ProgressReporterInterface $progressReporter,
    ) {
        parent::__construct($searchWpService, $progressReporter);
    }

    /**
     * Get the Search Index AJAX action name.
     */
    protected function actionName(): string
    {
        return self::ACTION;
    }

    /**
     * Get Search Index request configuration.
     */
    protected function config(): ProgressAjaxActionConfig
    {
        return new ProgressAjaxActionConfig(
            requiredCapability: 'manage_options',
            messages: new ProgressAjaxActionMessages(
                unauthorized: $this->searchWpService->__('You are not allowed to start search indexing.', 'municipio'),
                invalidMethod: $this->searchWpService->__('Search indexing must be started with a POST request.', 'municipio'),
                invalidNonce: $this->searchWpService->__('The indexing request could not be verified. Reload the page and try again.', 'municipio'),
            ),
            requiredMethod: 'POST',
            nonceAction: self::NONCE_ACTION,
        );
    }

    /**
     * Validate and execute an admin indexing request.
     */
    protected function execute(): string
    {
        $lockOwner = bin2hex(random_bytes(16));

        if (!$this->lock->acquire($lockOwner)) {
            return $this->searchWpService->__('Search indexing is already in progress.', 'municipio');
        }

        try {
            $indexedCount = $this->runner->run($lockOwner);
            return sprintf(
                $this->searchWpService->_n(
                    'Search indexing complete. Indexed %d item.',
                    'Search indexing complete. Indexed %d items.',
                    $indexedCount,
                    'municipio',
                ),
                $indexedCount,
            );
        } finally {
            $this->lock->release($lockOwner);
        }
    }

    /**
     * Hide provider failure details from the streamed response.
     */
    protected function failureMessage(\Throwable $throwable): string
    {
        return $this->searchWpService->__(
            'Search indexing failed. Check the provider configuration and try again.',
            'municipio',
        );
    }
}