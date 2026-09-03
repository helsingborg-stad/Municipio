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
     * Check whether the configured search provider is reachable.
     */
    public function check(array $arguments, array $associativeArguments): void
    {
        if (!$this->config->isConfigured()) {
            $this->callWpCli('error', 'The search provider must be configured before checking.');
            return;
        }

        try {
            $this->providerFactory->create()->search('', 1, 1);
        } catch (\Throwable $exception) {
            $this->callWpCli('error', sprintf(
                'The search provider is not reachable: %s',
                $exception->getMessage(),
            ));
            return;
        }

        $this->callWpCli('success', 'The search provider is configured and reachable.');
    }

    /**
     * Invoke the WP-CLI runtime without requiring its classes in web requests.
     */
    private function callWpCli(string $method, mixed ...$arguments): mixed
    {
        return call_user_func_array(['WP_CLI', $method], $arguments);
    }
}