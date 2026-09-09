<?php

declare(strict_types=1);

namespace {
    require_once __DIR__ . '/../../../SearchIndex/Cli/WP_CLI.php';
}

namespace Municipio\SchemaData\ExternalContent\Cli {
    use Municipio\SchemaData\ExternalContent\Config\SourceConfigInterface;
    use Municipio\SchemaData\ExternalContent\SyncHandler\SyncHandlerInterface;
    use Municipio\SchemaData\ExternalContent\SyncHandler\SyncInProgress\PostTypeSyncInProgressInterface;
    use PHPUnit\Framework\TestCase;

    /**
     * Tests the external content sync command.
     */
    class SyncExternalContentCommandTest extends TestCase
    {
        /**
         * Clear recorded WP-CLI calls before each test.
         */
        protected function setUp(): void
        {
            \WP_CLI::$calls = [];
        }

        /**
         * Verify the command is registered under the sync subcommand.
         */
        public function testRegistersSyncCommand(): void
        {
            $command = new SyncExternalContentCommand(
                [],
                $this->createStub(SyncHandlerInterface::class),
                $this->createStub(PostTypeSyncInProgressInterface::class),
            );

            $command->register();

            static::assertSame('add_command', \WP_CLI::$calls[0][0]);
            static::assertSame('municipio external-content sync', \WP_CLI::$calls[0][1][0]);
            static::assertSame([$command, 'sync'], \WP_CLI::$calls[0][1][1]);
        }

        /**
         * Verify an unconfigured post type is rejected without starting a sync.
         */
        public function testRejectsUnconfiguredPostType(): void
        {
            $syncHandler = $this->createMock(SyncHandlerInterface::class);
            $syncHandler->expects($this->never())->method('sync');
            $command = new SyncExternalContentCommand(
                [],
                $syncHandler,
                $this->createStub(PostTypeSyncInProgressInterface::class),
            );

            $command->sync(['place'], []);

            static::assertSame([
                ['error', ['No external content source is configured for post type "place".']],
            ], \WP_CLI::$calls);
        }

        /**
         * Verify an in-progress sync is rejected without starting another sync.
         */
        public function testRejectsWhenSyncAlreadyInProgress(): void
        {
            $sourceConfig = $this->createStub(SourceConfigInterface::class);
            $sourceConfig->method('getPostType')->willReturn('place');
            $inProgress = $this->createMock(PostTypeSyncInProgressInterface::class);
            $inProgress->method('isInProgress')->with('place')->willReturn(true);
            $syncHandler = $this->createMock(SyncHandlerInterface::class);
            $syncHandler->expects($this->never())->method('sync');
            $command = new SyncExternalContentCommand([$sourceConfig], $syncHandler, $inProgress);

            $command->sync(['place'], []);

            static::assertSame([
                ['error', ['Sync already in progress for post type "place".']],
            ], \WP_CLI::$calls);
        }

        /**
         * Verify a normal sync does not force re-syncing unchanged objects.
         */
        public function testSyncsConfiguredPostTypeWithoutForcing(): void
        {
            $sourceConfig = $this->createStub(SourceConfigInterface::class);
            $sourceConfig->method('getPostType')->willReturn('place');
            $inProgress = $this->createMock(PostTypeSyncInProgressInterface::class);
            $inProgress->method('isInProgress')->willReturn(false);
            $inProgress->expects($this->exactly(2))->method('setInProgress')->with('place', $this->anything());
            $syncHandler = $this->createMock(SyncHandlerInterface::class);
            $syncHandler->expects($this->once())->method('sync')->with('place', null, false);
            $command = new SyncExternalContentCommand([$sourceConfig], $syncHandler, $inProgress);

            $command->sync(['place'], []);

            static::assertSame([
                ['log', ['Syncing "place" from remote source...']],
                ['success', ['Sync completed for post type "place".']],
            ], \WP_CLI::$calls);
        }

        /**
         * Verify the --force flag is forwarded to the sync handler.
         */
        public function testForwardsForceFlagToSyncHandler(): void
        {
            $sourceConfig = $this->createStub(SourceConfigInterface::class);
            $sourceConfig->method('getPostType')->willReturn('place');
            $inProgress = $this->createMock(PostTypeSyncInProgressInterface::class);
            $inProgress->method('isInProgress')->willReturn(false);
            $syncHandler = $this->createMock(SyncHandlerInterface::class);
            $syncHandler->expects($this->once())->method('sync')->with('place', null, true);
            $command = new SyncExternalContentCommand([$sourceConfig], $syncHandler, $inProgress);

            $command->sync(['place'], ['force' => true]);

            static::assertSame([
                ['log', ['Syncing "place" from remote source (forcing update of all posts)...']],
                ['success', ['Sync completed for post type "place".']],
            ], \WP_CLI::$calls);
        }

        /**
         * Verify the in-progress state is cleared even if the sync throws.
         */
        public function testClearsInProgressStateWhenSyncFails(): void
        {
            $sourceConfig = $this->createStub(SourceConfigInterface::class);
            $sourceConfig->method('getPostType')->willReturn('place');
            $inProgressCalls = [];
            $inProgress = $this->createMock(PostTypeSyncInProgressInterface::class);
            $inProgress->method('isInProgress')->willReturn(false);
            $inProgress->method('setInProgress')
                ->willReturnCallback(function (string $postType, bool $value) use (&$inProgressCalls): void {
                    $inProgressCalls[] = [$postType, $value];
                });
            $syncHandler = $this->createMock(SyncHandlerInterface::class);
            $syncHandler->method('sync')->willThrowException(new \RuntimeException('boom'));
            $command = new SyncExternalContentCommand([$sourceConfig], $syncHandler, $inProgress);

            $this->expectException(\RuntimeException::class);

            try {
                $command->sync(['place'], []);
            } finally {
                static::assertSame([
                    ['place', true],
                    ['place', false],
                ], $inProgressCalls);
            }
        }
    }
}
