<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Provider\Algolia;

use Municipio\SearchIndex\Config\SearchIndexConfig;
use Municipio\SearchIndex\Provider\SearchProviderInterface;
use WpService\WpService;

/**
 * Creates the Algolia search index provider.
 */
class AlgoliaProviderFactory
{
    public function __construct(
        private WpService $wpService,
        private SearchIndexConfig $config,
    ) {}

    /**
     * Create an Algolia provider using the configured credentials.
     */
    public function create(): SearchProviderInterface
    {
        return new AlgoliaProvider(
            $this->wpService,
            $this->config->algoliaApplicationId(),
            $this->config->algoliaApiKey(),
            $this->config->indexName(),
        );
    }
}