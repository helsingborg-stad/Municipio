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
     * Send settings to the selected search provider.
     *
     * ## OPTIONS
     *
     * [--provider=<provider>]
     * : Search provider to prepare. Defaults to the configured provider.
     */
    public function prepare(array $arguments, array $associativeArguments): void
    {
        $providerName = $associativeArguments['provider'] ?? $this->config->provider();

        if (!is_string($providerName) || !$this->config->isProviderConfigured($providerName)) {
            $this->callWpCli('error', 'The selected search provider must be configured before preparing.');
            return;
        }

        $this->callWpCli('log', 'Sending provider settings...');
        $this->providerFactory->create($providerName)->setSettings();
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