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
     * Tests the search provider check command.
     */
    class CheckSearchIndexCommandTest extends TestCase
    {
        /**
         * Clear recorded WP-CLI calls before each test.
         */
        protected function setUp(): void
        {
            \WP_CLI::$calls = [];
        }

        /**
         * Verify the command is registered under the check subcommand.
         */
        public function testRegistersCheckCommand(): void
        {
            $command = new CheckSearchIndexCommand(
                $this->createStub(SearchIndexConfig::class),
                $this->createStub(SearchProviderFactory::class),
            );

            $command->register();

            static::assertSame('add_command', \WP_CLI::$calls[0][0]);
            static::assertSame('municipio search-index check', \WP_CLI::$calls[0][1][0]);
            static::assertSame([$command, 'check'], \WP_CLI::$calls[0][1][1]);
        }

        /**
         * Verify a successful read-only query reports the provider as reachable.
         */
        public function testReportsConfiguredProviderAsReachable(): void
        {
            $config = $this->createMock(SearchIndexConfig::class);
            $config->expects($this->once())->method('isConfigured')->willReturn(true);
            $provider = $this->createMock(SearchProviderInterface::class);
            $provider->expects($this->once())->method('search')->with('', 1, 1)->willReturn(['hits' => []]);
            $providerFactory = $this->createMock(SearchProviderFactory::class);
            $providerFactory->expects($this->once())->method('create')->with()->willReturn($provider);
            $command = new CheckSearchIndexCommand($config, $providerFactory);

            $command->check([], ['provider' => 'typesense']);

            static::assertSame([
                ['success', ['The search provider is configured and reachable.']],
            ], \WP_CLI::$calls);
        }

        /**
         * Verify an unconfigured provider is rejected without querying it.
         */
        public function testRejectsUnconfiguredProvider(): void
        {
            $config = $this->createMock(SearchIndexConfig::class);
            $config->expects($this->once())->method('isConfigured')->willReturn(false);
            $providerFactory = $this->createMock(SearchProviderFactory::class);
            $providerFactory->expects($this->never())->method('create');
            $command = new CheckSearchIndexCommand($config, $providerFactory);

            $command->check([], []);

            static::assertSame([
                ['error', ['The search provider must be configured before checking.']],
            ], \WP_CLI::$calls);
        }

        /**
         * Verify provider failures are reported as an unreachable provider.
         */
        public function testReportsProviderFailure(): void
        {
            $config = $this->createMock(SearchIndexConfig::class);
            $config->method('isConfigured')->willReturn(true);
            $provider = $this->createMock(SearchProviderInterface::class);
            $provider->method('search')->willThrowException(new \RuntimeException('Connection timed out.'));
            $providerFactory = $this->createMock(SearchProviderFactory::class);
            $providerFactory->method('create')->with()->willReturn($provider);
            $command = new CheckSearchIndexCommand($config, $providerFactory);

            $command->check([], []);

            static::assertSame([
                ['error', ['The search provider is not reachable: Connection timed out.']],
            ], \WP_CLI::$calls);
        }
    }
}