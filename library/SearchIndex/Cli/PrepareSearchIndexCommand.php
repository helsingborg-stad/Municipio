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
     * [--reset]
     * : Delete the existing collection/index before sending settings, so a schema change requiring a new
     * collection can be applied. Prompts for confirmation unless --yes is also passed.
     *
     * ## EXAMPLES
     *
     *     wp municipio search-index prepare
     *     wp municipio search-index prepare --reset
     */
    public function prepare(array $arguments, array $associativeArguments): void
    {
        if (!$this->config->isConfigured()) {
            $this->callWpCli('error', 'The search provider must be configured before preparing.');
            return;
        }

        $provider = $this->providerFactory->create();

        if (isset($associativeArguments['reset'])) {
            $this->callWpCli(
                'confirm',
                'This will permanently delete the existing search index collection/index and cannot be undone. Continue?',
                $associativeArguments,
            );
            $this->callWpCli('log', 'Resetting existing collection/index...');
            $provider->resetIndex();
        }

        $this->callWpCli('log', 'Sending provider settings...');
        $provider->setSettings();
        $this->callWpCli('success', 'Search index preparation complete.');

        if (isset($associativeArguments['reset'])) {
            $this->callWpCli('log', 'Run `wp municipio search-index build` to re-index all content.');
        }
    }


    /**
     * Invoke the WP-CLI runtime without requiring its classes in web requests.
     */
    private function callWpCli(string $method, mixed ...$arguments): mixed
    {
        return call_user_func_array(['WP_CLI', $method], $arguments);
    }
}