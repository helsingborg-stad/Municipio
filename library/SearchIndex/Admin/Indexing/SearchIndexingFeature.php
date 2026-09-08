<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Admin\Indexing;

use Municipio\ProgressReporter\HttpHeader\HttpHeader;
use Municipio\ProgressReporter\OutputBuffer\OutputBuffer;
use Municipio\ProgressReporter\SseProgressReporterService;
use Municipio\SearchIndex\Config\SearchIndexConfig;
use Municipio\SearchIndex\Provider\SearchProviderFactory;
use WpService\WpService;

/**
 * Composes the Search Index admin indexing module.
 */
class SearchIndexingFeature
{
    /**
     * Create the Search Index admin indexing module.
     */
    public function __construct(
        private WpService $wpService,
        private SearchIndexConfig $config,
        private SearchProviderFactory $providerFactory,
    ) {}

    /**
     * Register the indexing endpoint and admin UI.
     */
    public function addHooks(): void
    {
        $progressReporter = new SseProgressReporterService(new HttpHeader(), new OutputBuffer());
        $lock = new SearchIndexingLock($this->wpService);
        $runner = new SearchIndexingRunner(
            $this->wpService,
            $this->config,
            new PostIndexerFactory($this->wpService, $this->providerFactory),
            $lock,
            $progressReporter,
        );

        (new SearchIndexingRequest($this->wpService, $runner, $lock, $progressReporter))->addHooks();
        (new SearchIndexingAdmin($this->wpService, $this->config))->addHooks();
    }
}