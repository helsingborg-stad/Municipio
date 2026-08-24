<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Provider\Algolia;

use Municipio\SearchIndex\Config\SearchIndexConfig;
use WpService\WpService;

/**
 * Exposes browser-safe Algolia configuration to search UI integrations.
 */
class AlgoliaProviderRegistrar
{
    public function __construct(
        private WpService $wpService,
        private SearchIndexConfig $config,
    ) {}

    /**
     * Register browser configuration integration hooks.
     */
    public function addHooks(): void
    {
        $this->wpService->addFilter('Municipio/SearchIndex/BrowserConfig', [$this, 'addBrowserConfig']);
    }

    /**
     * Add the selected Algolia index's browser-safe connection settings.
     */
    public function addBrowserConfig(array $browserConfig): array
    {
        if ($this->config->provider() !== 'algolia' || $this->config->algoliaPublicApiKey() === '') {
            return $browserConfig;
        }

        return [
            ...$browserConfig,
            'type' => 'algolia',
            'applicationId' => $this->config->algoliaApplicationId(),
            'apiKey' => $this->config->algoliaPublicApiKey(),
            'indexName' => $this->config->indexName(),
        ];
    }
}