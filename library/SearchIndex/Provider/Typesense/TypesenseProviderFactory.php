<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Provider\Typesense;

use Municipio\SearchIndex\Config\SearchIndexConfig;
use Municipio\SearchIndex\Provider\SearchProviderInterface;
use WpService\WpService;

/**
 * Creates a Typesense search index provider from theme configuration.
 */
class TypesenseProviderFactory
{
    public function __construct(
        private WpService $wpService,
        private SearchIndexConfig $config,
    ) {}

    /**
     * Create a Typesense provider using configured credentials.
     */
    public function create(): SearchProviderInterface
    {
        return new TypesenseProvider(
            $this->wpService,
            $this->config->typesenseApiKey(),
            $this->config->typesenseApiUrl(),
            $this->config->typesenseCollectionName(),
        );
    }
}