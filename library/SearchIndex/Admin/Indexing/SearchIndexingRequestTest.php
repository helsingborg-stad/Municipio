<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Admin\Indexing;

use Municipio\ProgressReporter\ProgressReporterInterface;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

/**
 * Tests authenticated Search Index admin indexing requests.
 */
class SearchIndexingRequestTest extends TestCase
{
    /**
     * Use the required HTTP method unless a test overrides it.
     */
    protected function setUp(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
    }

    /**
     * Remove request state after each test.
     */
    protected function tearDown(): void
    {
        unset($_SERVER['REQUEST_METHOD']);
    }

    /**
     * Verify users without settings access cannot start indexing.
     */
    public function testRejectsUnauthorizedUser(): void
    {
        $messages = [];
        $reporter = $this->createProgressReporter($messages);
        $lock = $this->createMock(SearchIndexingLockInterface::class);
        $lock->expects($this->never())->method('acquire');
        $request = new SearchIndexingRequest(
            new FakeWpService([
                'currentUserCan' => false,
                '__' => static fn(string $text): string => $text,
            ]),
            $this->createMock(SearchIndexingRunnerInterface::class),
            $lock,
            $reporter,
        );

        $request->handleRequest();

        static::assertSame(['You are not allowed to start search indexing.'], $messages);
    }

    /**
     * Verify requests with an invalid nonce cannot start indexing.
     */
    public function testRejectsInvalidNonce(): void
    {
        $messages = [];
        $reporter = $this->createProgressReporter($messages);
        $lock = $this->createMock(SearchIndexingLockInterface::class);
        $lock->expects($this->never())->method('acquire');
        $request = new SearchIndexingRequest(
            new FakeWpService([
                'currentUserCan' => true,
                'checkAjaxReferer' => false,
                '__' => static fn(string $text): string => $text,
            ]),
            $this->createMock(SearchIndexingRunnerInterface::class),
            $lock,
            $reporter,
        );

        $request->handleRequest();

        static::assertSame(['The indexing request could not be verified. Reload the page and try again.'], $messages);
    }

    /**
     * Verify indexing cannot be started with a GET request.
     */
    public function testRejectsGetRequest(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $messages = [];
        $reporter = $this->createProgressReporter($messages);
        $lock = $this->createMock(SearchIndexingLockInterface::class);
        $lock->expects($this->never())->method('acquire');
        $request = new SearchIndexingRequest($this->authorizedWpService(), $this->createMock(SearchIndexingRunnerInterface::class), $lock, $reporter);

        $request->handleRequest();

        static::assertSame(['Search indexing must be started with a POST request.'], $messages);
    }

    /**
     * Verify an active lock prevents a concurrent indexing run.
     */
    public function testRejectsConcurrentRequest(): void
    {
        $messages = [];
        $reporter = $this->createProgressReporter($messages);
        $lock = $this->createMock(SearchIndexingLockInterface::class);
        $lock->method('acquire')->willReturn(false);
        $runner = $this->createMock(SearchIndexingRunnerInterface::class);
        $runner->expects($this->never())->method('run');
        $request = new SearchIndexingRequest($this->authorizedWpService(), $runner, $lock, $reporter);

        $request->handleRequest();

        static::assertSame(['Search indexing is already in progress.'], $messages);
    }

    /**
     * Verify a successful request releases its lock and reports the indexed count.
     */
    public function testCompletesIndexingAndReleasesLock(): void
    {
        $messages = [];
        $reporter = $this->createProgressReporter($messages);
        $lock = $this->createMock(SearchIndexingLockInterface::class);
        $lock->method('acquire')->willReturn(true);
        $lock->expects($this->once())->method('release');
        $runner = $this->createMock(SearchIndexingRunnerInterface::class);
        $runner->method('run')->willReturn(2);
        $request = new SearchIndexingRequest($this->authorizedWpService(), $runner, $lock, $reporter);

        $request->handleRequest();

        static::assertSame(['Search indexing complete. Indexed 2 items.'], $messages);
    }

    /**
     * Verify indexing failures still release the lock and return a safe message.
     */
    public function testReleasesLockAfterFailure(): void
    {
        $messages = [];
        $reporter = $this->createProgressReporter($messages);
        $lock = $this->createMock(SearchIndexingLockInterface::class);
        $lock->method('acquire')->willReturn(true);
        $lock->expects($this->once())->method('release');
        $runner = $this->createMock(SearchIndexingRunnerInterface::class);
        $runner->method('run')->willThrowException(new \RuntimeException('Secret provider error'));
        $request = new SearchIndexingRequest($this->authorizedWpService(), $runner, $lock, $reporter);

        $request->handleRequest();

        static::assertSame(['Search indexing failed. Check the provider configuration and try again.'], $messages);
    }

    /**
     * Create a WordPress service for an authorized, verified request.
     */
    private function authorizedWpService(): FakeWpService
    {
        return new FakeWpService([
            'currentUserCan' => true,
            'checkAjaxReferer' => 1,
            '__' => static fn(string $text): string => $text,
            '_n' => static fn(string $single, string $plural, int $count): string => $count === 1 ? $single : $plural,
        ]);
    }

    /**
     * Create a progress reporter that records completion messages.
     *
     * @param array<int, string> $messages
     */
    private function createProgressReporter(array &$messages): ProgressReporterInterface
    {
        return new class ($messages) implements ProgressReporterInterface {
            /** Create a recording progress reporter. */
            public function __construct(private array &$messages) {}

            /** Start progress reporting. */
            public function start(): void {}

            /** Ignore intermediate messages. */
            public function setMessage(string $message): void {}

            /** Ignore percentages. */
            public function setPercentage(int|float $percentage): void {}

            /** Record the final message. */
            public function finish(string $message): void
            {
                $this->messages[] = $message;
            }
        };
    }
}