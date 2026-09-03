<?php

declare(strict_types=1);

namespace {
    require_once __DIR__ . '/WP_CLI.php';
}

namespace Municipio\SearchIndex\Cli {
    use Municipio\SearchIndex\Config\SearchIndexConfig;
    use Municipio\SearchIndex\Provider\SearchProviderFactory;
    use Municipio\SearchIndex\Provider\SearchProviderInterface;
    use PHPUnit\Framework\TestCase;
    use WpService\Implementations\FakeWpService;

    /**
     * Tests the search index build command.
     */
    class BuildSearchIndexCommandTest extends TestCase
    {
        /**
         * Clear recorded WP-CLI calls before each test.
         */
        protected function setUp(): void
        {
            \WP_CLI::$calls = [];
        }

        /**
         * Verify the command is registered under the build subcommand.
         */
        public function testRegistersBuildCommand(): void
        {
            $command = new BuildSearchIndexCommand(
                new FakeWpService(),
                $this->createStub(SearchIndexConfig::class),
                $this->createStub(SearchProviderFactory::class),
            );

            $command->register();

            static::assertSame('add_command', \WP_CLI::$calls[0][0]);
            static::assertSame('municipio search-index build', \WP_CLI::$calls[0][1][0]);
            static::assertSame([$command, 'build'], \WP_CLI::$calls[0][1][1]);
        }

        /**
         * Verify an unconfigured provider prevents the build from starting.
         */
        public function testRejectsUnconfiguredProvider(): void
        {
            $config = $this->createMock(SearchIndexConfig::class);
            $config->expects($this->once())->method('isConfigured')->willReturn(false);
            $providerFactory = $this->createMock(SearchProviderFactory::class);
            $providerFactory->expects($this->never())->method('create');
            $command = new BuildSearchIndexCommand(new FakeWpService(), $config, $providerFactory);

            $command->build([], []);

            static::assertSame([
                ['error', ['The search provider must be configured before indexing.']],
            ], \WP_CLI::$calls);
        }

        /**
         * Verify a normal build neither clears the provider nor accepts provider overrides.
         */
        public function testBuildsConfiguredProviderWithoutClearing(): void
        {
            $config = $this->createMock(SearchIndexConfig::class);
            $config->method('isConfigured')->willReturn(true);
            $provider = $this->createMock(SearchProviderInterface::class);
            $provider->expects($this->never())->method('clearObjects');
            $provider->expects($this->never())->method('setSettings');
            $providerFactory = $this->createMock(SearchProviderFactory::class);
            $providerFactory->expects($this->once())->method('create')->with()->willReturn($provider);
            $wpService = new FakeWpService([
                'getOption' => 'https://example.test',
                'getPostTypes' => [],
                'applyFilters' => static fn(string $hook, mixed $value): mixed => $value,
            ]);
            $command = new BuildSearchIndexCommand($wpService, $config, $providerFactory);

            $command->build([], ['provider' => 'typesense', 'settings' => true]);

            static::assertSame([
                ['log', ['Starting search index build for site https://example.test']],
                ['success', ['Search index build complete.']],
            ], \WP_CLI::$calls);
        }

        /**
         * Verify clearing, post type filtering, status filtering, and pagination.
         */
        public function testClearsAndBuildsAllFilteredPostPages(): void
        {
            $queries = [];
            $filterCalls = [];
            $post = new \WP_Post((object) []);
            $post->ID = 42;
            $post->post_title = 'Indexed post';
            $wpService = new FakeWpService([
                'getOption' => 'https://example.test',
                'getPostTypes' => ['post', 'attachment'],
                'getPosts' => static function (array $query) use (&$queries, $post): array {
                    $queries[] = $query;
                    return $query['paged'] === 1 ? [$post] : [];
                },
                'getPost' => null,
                'applyFilters' => static function (string $hook, mixed $value) use (&$filterCalls): mixed {
                    $filterCalls[] = [$hook, $value];
                    return $hook === 'Municipio/SearchIndex/IndexablePostStatuses' ? ['publish', 'private'] : $value;
                },
            ]);
            $config = $this->createMock(SearchIndexConfig::class);
            $config->method('isConfigured')->willReturn(true);
            $provider = $this->createMock(SearchProviderInterface::class);
            $provider->expects($this->once())->method('clearObjects');
            $providerFactory = $this->createMock(SearchProviderFactory::class);
            $providerFactory->method('create')->willReturn($provider);
            $command = new BuildSearchIndexCommand($wpService, $config, $providerFactory);

            $command->build([], ['clearindex' => 'true']);

            static::assertSame([1, 2], array_column($queries, 'paged'));
            static::assertSame(['post'], array_unique(array_column($queries, 'post_type')));
            static::assertSame(['publish', 'private'], $queries[0]['post_status']);
            static::assertContains(
                ['Municipio/SearchIndex/IndexablePostTypes', ['post']],
                $filterCalls,
            );
            static::assertSame([
                ['log', ['Clearing existing search index records...']],
                ['log', ['Starting search index build for site https://example.test']],
                ['log', ["Indexing 'Indexed post' of post type 'post'"]],
                ['success', ['Search index build complete.']],
            ], \WP_CLI::$calls);
        }
    }
}