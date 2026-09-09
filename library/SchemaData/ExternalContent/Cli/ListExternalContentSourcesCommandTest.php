<?php

declare(strict_types=1);

namespace {
    require_once __DIR__ . '/WP_CLI.php';
}

namespace Municipio\SchemaData\ExternalContent\Cli {
    use Municipio\SchemaData\ExternalContent\Config\SourceConfigInterface;
    use PHPUnit\Framework\TestCase;

    /**
     * Tests the external content list command.
     */
    class ListExternalContentSourcesCommandTest extends TestCase
    {
        /**
         * Clear recorded WP-CLI calls before each test.
         */
        protected function setUp(): void
        {
            \WP_CLI::$calls = [];
        }

        /**
         * Verify the command is registered under the list subcommand.
         */
        public function testRegistersListCommand(): void
        {
            $command = new ListExternalContentSourcesCommand([]);

            $command->register();

            static::assertSame('add_command', \WP_CLI::$calls[0][0]);
            static::assertSame('municipio external-content list', \WP_CLI::$calls[0][1][0]);
            static::assertSame([$command, 'list'], \WP_CLI::$calls[0][1][1]);
        }

        /**
         * Verify an informative message is logged when no sources are configured.
         */
        public function testLogsMessageWhenNoSourcesAreConfigured(): void
        {
            $command = new ListExternalContentSourcesCommand([]);

            $command->list([], []);

            static::assertSame([
                ['log', ['No external content sources are configured.']],
            ], \WP_CLI::$calls);
        }

        /**
         * Verify each configured source is logged with its details.
         */
        public function testLogsEachConfiguredSource(): void
        {
            $sourceConfig = $this->createStub(SourceConfigInterface::class);
            $sourceConfig->method('getPostType')->willReturn('place');
            $sourceConfig->method('getSchemaType')->willReturn('Place');
            $sourceConfig->method('getSourceType')->willReturn('json-file');
            $sourceConfig->method('getAutomaticImportSchedule')->willReturn('hourly');
            $command = new ListExternalContentSourcesCommand([$sourceConfig]);

            $command->list([], []);

            static::assertSame([
                ['log', ['place (schema: Place, source: json-file, schedule: hourly)']],
            ], \WP_CLI::$calls);
        }
    }
}
