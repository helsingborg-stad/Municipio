<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Provider\Typesense;

use Municipio\SearchIndex\Config\SearchIndexConfig;
use Municipio\SearchIndex\Provider\SearchProviderInterface;
use WpService\WpService;

/**
 * Registers Typesense with the Search Index provider and browser integrations.
 */
class TypesenseProviderRegistrar
{
    public function __construct(
        private WpService $wpService,
        private SearchIndexConfig $config,
    ) {}

    /**
     * Register Typesense provider, browser configuration, and CSP hooks.
     */
    public function addHooks(): void
    {
        $this->wpService->addFilter('Municipio/SearchIndex/ProviderFactory', [$this, 'registerProvider']);
        $this->wpService->addFilter('Municipio/SearchIndex/BrowserConfig', [$this, 'addBrowserConfig']);
        $this->wpService->addFilter('WpSecurity/Csp', [$this, 'addCspDomain']);
    }

    /**
     * Add Typesense to the available provider factories.
     *
     * @param array<string, callable(): SearchProviderInterface> $providers
     * @return array<string, callable(): SearchProviderInterface>
     */
    public function registerProvider(array $providers): array
    {
        $providers['typesense'] = fn(): SearchProviderInterface => (new TypesenseProviderFactory($this->wpService, $this->config))->create();

        return $providers;
    }

    /**
     * Expose the browser-safe Typesense configuration to search UI integrations.
     */
    public function addBrowserConfig(array $browserConfig): array
    {
        if ($this->config->provider() !== 'typesense' || $this->config->typesensePublicApiKey() === '') {
            return $browserConfig;
        }

        $urlParts = parse_url($this->config->typesenseApiUrl());
        $host = is_array($urlParts) ? $urlParts['host'] ?? null : null;

        if (!is_string($host) || $host === '') {
            return $browserConfig;
        }

        return [
            ...$browserConfig,
            'type' => 'typesense',
            'host' => $host,
            'port' => $urlParts['port'] ?? (($urlParts['scheme'] ?? 'https') === 'http' ? 80 : 443),
            'protocol' => $urlParts['scheme'] ?? 'https',
            'apiKey' => $this->config->typesensePublicApiKey(),
            'collectionName' => $this->config->typesenseCollectionName(),
        ];
    }

    /**
     * Allow browser search clients to connect to the configured Typesense host.
     */
    public function addCspDomain(array $directives): array
    {
        if ($this->config->provider() !== 'typesense') {
            return $directives;
        }

        $urlParts = parse_url($this->config->typesenseApiUrl());
        $host = is_array($urlParts) ? $urlParts['host'] ?? null : null;

        if (!is_string($host) || $host === '') {
            return $directives;
        }

        $directives['connect-src'] ??= [];
        $directives['connect-src'][] = sprintf('%s://%s', $urlParts['scheme'] ?? 'https', $host);

        return $directives;
    }
}