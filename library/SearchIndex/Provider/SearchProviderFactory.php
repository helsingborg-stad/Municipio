<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Provider;

use Municipio\SearchIndex\Config\SearchIndexConfig;
use Municipio\SearchIndex\Provider\Algolia\AlgoliaProviderFactory;
use WpService\WpService;

/**
 * Creates the configured search index provider.
 */
class SearchProviderFactory
{
    private const DEFAULT_PROVIDER = 'algolia';

    public function __construct(
        private WpService $wpService,
        private SearchIndexConfig $config,
    ) {}

    /**
     * Get the registered provider factories.
     *
     * @return array<string, callable(): SearchProviderInterface>
     */
    public function getProviders(): array
    {
        return $this->wpService->applyFilters('Municipio/SearchIndex/ProviderFactory', [
            self::DEFAULT_PROVIDER => fn(): SearchProviderInterface => (new AlgoliaProviderFactory($this->wpService, $this->config))->create(),
        ]);
    }

    /**
     * Create the selected provider.
     */
    public function create(?string $provider = null): SearchProviderInterface
    {
        $providers = $this->getProviders();
        $provider ??= $this->config->provider() ?? self::DEFAULT_PROVIDER;

        if (!array_key_exists($provider, $providers)) {
            $provider = self::DEFAULT_PROVIDER;
        }

        $factory = $providers[$provider] ?? null;

        if (!is_callable($factory)) {
            throw new \InvalidArgumentException(sprintf('Search provider "%s" is not available.', $provider));
        }

        return $factory();
    }
}