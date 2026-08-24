<?php

declare(strict_types=1);

namespace Municipio\SearchIndex;

use AcfService\Contracts\AddOptionsPage;
use AcfService\Contracts\GetField;
use Municipio\SearchIndex\Config\SearchIndexConfig;
use Municipio\SearchIndex\Provider\SearchProviderFactory;
use WpService\WpService;

/**
 * Enables indexing and querying content through the configured search provider.
 */
class SearchIndexFeature
{
    public function __construct(
        private WpService $wpService,
        private GetField&AddOptionsPage $acfService,
    ) {}

    /**
     * Register the feature hooks when a search provider has been configured.
     */
    public function enable(): void
    {
        $config = new SearchIndexConfig($this->acfService);
        $providerFactory = new SearchProviderFactory($this->wpService, $config);

        (new Admin\SearchIndexSettings($this->wpService, $this->acfService, $config, $providerFactory))->addHooks();
        (new Admin\ExcludeFromSearch($this->wpService))->addHooks();

        if (!$config->isConfigured()) {
            return;
        }

        $provider = $providerFactory->create();
        (new Index\PostIndexer($this->wpService, $provider))->addHooks();
        (new Search\SearchQuery($this->wpService, $provider))->addHooks();
    }
}