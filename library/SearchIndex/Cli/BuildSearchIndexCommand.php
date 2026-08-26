<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Cli;

use Municipio\SearchIndex\Config\SearchIndexConfig;
use Municipio\SearchIndex\Index\PostIndexer;
use Municipio\SearchIndex\Provider\SearchProviderFactory;
use WpService\WpService;

/**
 * Rebuilds a configured search index from all eligible WordPress posts.
 */
class BuildSearchIndexCommand
{
    public function __construct(
        private WpService $wpService,
        private SearchIndexConfig $config,
        private SearchProviderFactory $providerFactory,
    ) {}

    /**
     * Register the provider-neutral WP-CLI build command.
     */
    public function register(): void
    {
        $this->callWpCli('add_command', 'municipio search-index build', [$this, 'build']);
    }

    /**
     * Rebuild the selected provider's search index.
     *
     * ## OPTIONS
     *
     * [--provider=<provider>]
     * : Search provider to build. Defaults to the configured provider.
     *
     * [--settings]
     * : Send provider settings before indexing posts.
     *
     * [--clearindex]
     * : Clear provider records before indexing posts.
     */
    public function build(array $arguments, array $associativeArguments): void
    {
        $providerName = $associativeArguments['provider'] ?? $this->config->provider();

        if (!is_string($providerName) || !$this->config->isProviderConfigured($providerName)) {
            $this->callWpCli('error', 'The selected search provider must be configured before indexing.');
            return;
        }

        $provider = $this->providerFactory->create($providerName);

        if ($this->hasFlag($associativeArguments, 'settings')) {
            $this->callWpCli('log', 'Sending provider settings...');
            $provider->setSettings();
        }

        if ($this->hasFlag($associativeArguments, 'clearindex')) {
            $this->callWpCli('log', 'Clearing existing search index records...');
            $provider->clearObjects();
        }

        $this->callWpCli('log', 'Starting search index build for site ' . $this->wpService->getOption('home'));
        $indexer = new PostIndexer($this->wpService, $provider);

        foreach ($this->getIndexablePostTypes() as $postType) {
            for ($page = 1; ; $page++) {
                $posts = $this->wpService->getPosts([
                    'post_type' => $postType,
                    'post_status' => $this->wpService->applyFilters('Municipio/SearchIndex/IndexablePostStatuses', ['publish']),
                    'numberposts' => 100,
                    'paged' => $page,
                    'suppress_filters' => false,
                ]);
                if ($posts === []) {
                    break;
                }
                foreach ($posts as $post) {
                    $this->callWpCli('log', sprintf("Indexing '%s' of post type '%s'", $post->post_title, $postType));
                    $indexer->index($post);
                }
            }
        }

        $this->callWpCli('success', 'Search index build complete.');
    }

    /**
     * Get public WordPress post types that should be indexed.
     *
     * @return array<int, string>
     */
    private function getIndexablePostTypes(): array
    {
        $postTypes = array_values(array_diff($this->wpService->getPostTypes([
            'public' => true,
            'exclude_from_search' => false,
        ]), ['attachment']));

        return $this->wpService->applyFilters('Municipio/SearchIndex/IndexablePostTypes', $postTypes);
    }

    /**
     * Check whether a boolean WP-CLI flag has been enabled.
     */
    private function hasFlag(array $associativeArguments, string $flag): bool
    {
        return ($associativeArguments[$flag] ?? false) === true
            || ($associativeArguments[$flag] ?? '') === 'true';
    }

    /**
     * Invoke the WP-CLI runtime without requiring its classes in web requests.
     */
    private function callWpCli(string $method, mixed ...$arguments): mixed
    {
        return call_user_func_array(['WP_CLI', $method], $arguments);
    }
}