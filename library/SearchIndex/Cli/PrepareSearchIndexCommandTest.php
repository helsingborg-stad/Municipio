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

    /**
     * Tests the search index preparation command.
     */
    class PrepareSearchIndexCommandTest extends TestCase
    {
        /**
         * Clear recorded WP-CLI calls before each test.
         */
        protected function setUp(): void
        {
            \WP_CLI::$calls = [];
        }

        /**
         * Verify the command is registered under the prepare subcommand.
         */
        public function testRegistersPrepareCommand(): void
        {
            $command = new PrepareSearchIndexCommand(
                $this->createStub(SearchIndexConfig::class),
                $this->createStub(SearchProviderFactory::class),
            );

            $command->register();

            static::assertSame('add_command', \WP_CLI::$calls[0][0]);
            static::assertSame('municipio search-index prepare', \WP_CLI::$calls[0][1][0]);
            static::assertSame([$command, 'prepare'], \WP_CLI::$calls[0][1][1]);
        }

        /**
         * Verify preparation sends settings to the configured provider.
         */
        public function testPreparesConfiguredProvider(): void
        {
            $config = $this->createMock(SearchIndexConfig::class);
            $config->method('provider')->willReturn('algolia');
            $config->method('isProviderConfigured')->with('algolia')->willReturn(true);
            $provider = $this->createMock(SearchProviderInterface::class);
            $provider->expects($this->once())->method('setSettings');
            $providerFactory = $this->createMock(SearchProviderFactory::class);
            $providerFactory->expects($this->once())->method('create')->with('algolia')->willReturn($provider);
            $command = new PrepareSearchIndexCommand($config, $providerFactory);

            $command->prepare([], []);

            static::assertSame([
                ['log', ['Sending provider settings...']],
                ['success', ['Search index preparation complete.']],
            ], \WP_CLI::$calls);
        }

        /**
         * Verify an unconfigured provider is rejected without creating it.
         */
        public function testRejectsUnconfiguredProvider(): void
        {
            $config = $this->createMock(SearchIndexConfig::class);
            $config->expects($this->once())->method('isProviderConfigured')->with('typesense')->willReturn(false);
            $providerFactory = $this->createMock(SearchProviderFactory::class);
            $providerFactory->expects($this->never())->method('create');
            $command = new PrepareSearchIndexCommand($config, $providerFactory);

            $command->prepare([], ['provider' => 'typesense']);

            static::assertSame([
                ['error', ['The selected search provider must be configured before preparing.']],
            ], \WP_CLI::$calls);
        }
    }
}