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
     * Tests the search index clear command.
     */
    class ClearSearchIndexCommandTest extends TestCase
    {
        /**
         * Clear recorded WP-CLI calls before each test.
         */
        protected function setUp(): void
        {
            \WP_CLI::$calls = [];
        }

        /**
         * Verify the command is registered under the clear subcommand.
         */
        public function testRegistersClearCommand(): void
        {
            $command = new ClearSearchIndexCommand(
                $this->createStub(SearchIndexConfig::class),
                $this->createStub(SearchProviderFactory::class),
            );

            $command->register();

            static::assertSame('add_command', \WP_CLI::$calls[0][0]);
            static::assertSame('municipio search-index clear', \WP_CLI::$calls[0][1][0]);
            static::assertSame([$command, 'clear'], \WP_CLI::$calls[0][1][1]);
        }

        /**
         * Verify clearing removes current-site records from the configured provider.
         */
        public function testClearsConfiguredProvider(): void
        {
            $config = $this->createMock(SearchIndexConfig::class);
            $config->method('isConfigured')->willReturn(true);
            $provider = $this->createMock(SearchProviderInterface::class);
            $provider->expects($this->once())->method('clearObjects');
            $providerFactory = $this->createMock(SearchProviderFactory::class);
            $providerFactory->expects($this->once())->method('create')->with()->willReturn($provider);
            $command = new ClearSearchIndexCommand($config, $providerFactory);

            $command->clear([], []);

            static::assertSame([
                ['log', ['Clearing existing search index records...']],
                ['success', ['Search index cleared.']],
            ], \WP_CLI::$calls);
        }

        /**
         * Verify an unconfigured provider is rejected without creating it.
         */
        public function testRejectsUnconfiguredProvider(): void
        {
            $config = $this->createMock(SearchIndexConfig::class);
            $config->expects($this->once())->method('isConfigured')->willReturn(false);
            $providerFactory = $this->createMock(SearchProviderFactory::class);
            $providerFactory->expects($this->never())->method('create');
            $command = new ClearSearchIndexCommand($config, $providerFactory);

            $command->clear([], []);

            static::assertSame([
                ['error', ['The search provider must be configured before clearing.']],
            ], \WP_CLI::$calls);
        }
    }
}