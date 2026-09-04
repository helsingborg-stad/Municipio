<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Cli;

use Municipio\SearchIndex\Config\SearchIndexConfig;
use Municipio\SearchIndex\Provider\SearchProviderFactory;

/**
 * Clears records from a configured search provider.
 */
class ClearSearchIndexCommand
{
    public function __construct(
        private SearchIndexConfig $config,
        private SearchProviderFactory $providerFactory,
    ) {}

    /**
     * Register the provider-neutral WP-CLI clear command.
     */
    public function register(): void
    {
        $this->callWpCli('add_command', 'municipio search-index clear', [$this, 'clear']);
    }

    /**
     * Clear records belonging to the current site from the configured provider.
     */
    public function clear(array $arguments, array $associativeArguments): void
    {
        if (!$this->config->isConfigured()) {
            $this->callWpCli('error', 'The search provider must be configured before clearing.');
            return;
        }

        $this->callWpCli('log', 'Clearing existing search index records...');
        $this->providerFactory->create()->clearObjects();
        $this->callWpCli('success', 'Search index cleared.');
    }

    /**
     * Invoke the WP-CLI runtime without requiring its classes in web requests.
     */
    private function callWpCli(string $method, mixed ...$arguments): mixed
    {
        return call_user_func_array(['WP_CLI', $method], $arguments);
    }
}