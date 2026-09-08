<?php

declare(strict_types=1);

namespace Municipio\SchemaData\ExternalContent\Rest;

use Municipio\ProgressReporter\AjaxAction\RecordingProgressReporter;
use Municipio\SchemaData\ExternalContent\SyncHandler\SyncHandlerInterface;
use Municipio\SchemaData\ExternalContent\SyncHandler\SyncInProgress\PostTypeSyncInProgressInterface;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

/**
 * Tests validation for external content progress requests.
 */
class AjaxSyncTest extends TestCase
{
    /**
     * Remove request state after each test.
     */
    protected function tearDown(): void
    {
        $_GET = [];
    }

    /**
     * Verify unauthorized users cannot start synchronization.
     */
    public function testRejectsUnauthorizedUser(): void
    {
        $reporter = new RecordingProgressReporter();
        $request = $this->createRequest([
            'currentUserCan' => false,
            '__' => static fn(string $text): string => $text,
        ], $reporter);

        $request->handleRequest();

        static::assertSame(['You are not allowed to sync external content.'], $reporter->messages);
    }

    /**
     * Verify invalid nonces cannot start synchronization.
     */
    public function testRejectsInvalidNonce(): void
    {
        $reporter = new RecordingProgressReporter();
        $request = $this->createRequest([
            'currentUserCan' => true,
            'checkAjaxReferer' => false,
            '__' => static fn(string $text): string => $text,
        ], $reporter);

        $request->handleRequest();

        static::assertSame(
            ['The sync request could not be verified. Reload the page and try again.'],
            $reporter->messages,
        );
    }

    /**
     * Create an external content sync request.
     *
     * @param array<string, mixed> $wpServiceMethods
     */
    private function createRequest(
        array $wpServiceMethods,
        RecordingProgressReporter $reporter,
    ): AjaxSync {
        return new AjaxSync(
            [],
            $this->createMock(PostTypeSyncInProgressInterface::class),
            $reporter,
            $this->createMock(SyncHandlerInterface::class),
            new FakeWpService($wpServiceMethods),
        );
    }
}