<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Cli;

use Municipio\SearchIndex\Config\SearchIndexConfig;
use Municipio\SearchIndex\Provider\SearchProviderFactory;

/**
 * Prepares a configured search provider for indexing.
 */
class PrepareSearchIndexCommand
{
    public function __construct(
        private SearchIndexConfig $config,
        private SearchProviderFactory $providerFactory,
    ) {}

    /**
     * Register the provider-neutral WP-CLI prepare command.
     */
    public function register(): void
    {
        $this->callWpCli('add_command', 'municipio search-index prepare', [$this, 'prepare']);
    }

    /**
     * Send settings to the configured search provider.
     *
     * ## OPTIONS
     *
     * [--provider=<provider>]
     * : Prepare this provider instead of the provider selected in Search Index settings.
     */
    public function prepare(array $arguments, array $associativeArguments): void
    {
        $provider = isset($associativeArguments['provider'])
            ? (string) $associativeArguments['provider']
            : null;
        $isConfigured = $provider === null
            ? $this->config->isConfigured()
            : $this->config->isProviderConfigured($provider);

        if (!$isConfigured) {
            $this->callWpCli('error', 'The search provider must be configured before preparing.');
            return;
        }

        $this->callWpCli('log', 'Sending provider settings...');
        $this->providerFactory->create($provider)->setSettings();
        $this->callWpCli('success', 'Search index preparation complete.');
    }

    /**
     * Invoke the WP-CLI runtime without requiring its classes in web requests.
     */
    private function callWpCli(string $method, mixed ...$arguments): mixed
    {
        return call_user_func_array(['WP_CLI', $method], $arguments);
    }
}