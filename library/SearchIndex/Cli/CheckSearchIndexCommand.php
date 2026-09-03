<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Cli;

use Municipio\SearchIndex\Config\SearchIndexConfig;
use Municipio\SearchIndex\Provider\SearchProviderFactory;

/**
 * Checks whether a configured search provider is reachable.
 */
class CheckSearchIndexCommand
{
    public function __construct(
        private SearchIndexConfig $config,
        private SearchProviderFactory $providerFactory,
    ) {}

    /**
     * Register the provider-neutral WP-CLI check command.
     */
    public function register(): void
    {
        $this->callWpCli('add_command', 'municipio search-index check', [$this, 'check']);
    }

    /**
     * Check whether the selected search provider is configured and reachable.
     *
     * ## OPTIONS
     *
     * [--provider=<provider>]
     * : Search provider to check. Defaults to the configured provider.
     */
    public function check(array $arguments, array $associativeArguments): void
    {
        $providerName = $associativeArguments['provider'] ?? $this->config->provider();

        if (!is_string($providerName) || !$this->config->isProviderConfigured($providerName)) {
            $this->callWpCli('error', 'The selected search provider must be configured before checking.');
            return;
        }

        try {
            $this->providerFactory->create($providerName)->search('', 1, 1);
        } catch (\Throwable $exception) {
            $this->callWpCli('error', sprintf(
                'Search provider "%s" is not reachable: %s',
                $providerName,
                $exception->getMessage(),
            ));
            return;
        }

        $this->callWpCli('success', sprintf(
            'Search provider "%s" is configured and reachable.',
            $providerName,
        ));
    }

    /**
     * Invoke the WP-CLI runtime without requiring its classes in web requests.
     */
    private function callWpCli(string $method, mixed ...$arguments): mixed
    {
        return call_user_func_array(['WP_CLI', $method], $arguments);
    }
}