<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Admin\Indexing;

use Municipio\ProgressReporter\ProgressReporterInterface;
use Municipio\SearchIndex\Config\SearchIndexConfig;
use Municipio\SearchIndex\Index\PostIndexer;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

/**
 * Tests provider-neutral admin indexing progress.
 */
class SearchIndexingRunnerTest extends TestCase
{
    /**
     * Verify all discovered posts are indexed with count-based progress.
     */
    public function testIndexesEligiblePostsAndReportsProgress(): void
    {
        $wpService = new FakeWpService([
            'getPostTypes' => ['post'],
            'applyFilters' => static fn(string $hook, mixed $value): mixed => $value,
            'getPosts' => [12, 34],
            '__' => static fn(string $text): string => $text,
        ]);
        $config = $this->createMock(SearchIndexConfig::class);
        $config->method('isConfigured')->willReturn(true);
        $indexedPostIds = [];
        $postIndexer = $this->createMock(PostIndexer::class);
        $postIndexer->expects($this->exactly(2))->method('index')->willReturnCallback(
            static function (int $postId) use (&$indexedPostIds): void {
                $indexedPostIds[] = $postId;
            }
        );
        $postIndexerFactory = $this->createMock(PostIndexerFactoryInterface::class);
        $postIndexerFactory->method('create')->willReturn($postIndexer);
        $lock = $this->createMock(SearchIndexingLockInterface::class);
        $lock->expects($this->exactly(2))->method('refresh')->with('owner');
        $messages = [];
        $percentages = [];
        $progressReporter = $this->createProgressReporter($messages, $percentages);
        $runner = new SearchIndexingRunner($wpService, $config, $postIndexerFactory, $lock, $progressReporter);

        $indexedCount = $runner->run('owner');

        static::assertSame(2, $indexedCount);
        static::assertSame([12, 34], $indexedPostIds);
        static::assertSame(['Indexing 1/2', 'Indexing 2/2'], $messages);
        static::assertSame([50.0, 100], $percentages);
        static::assertSame('ids', $wpService->methodCalls['getPosts'][0][0]['fields']);
        static::assertSame(-1, $wpService->methodCalls['getPosts'][0][0]['numberposts']);
        static::assertArrayNotHasKey('paged', $wpService->methodCalls['getPosts'][0][0]);
    }

    /**
     * Verify an unconfigured provider is rejected before post discovery.
     */
    public function testRejectsUnconfiguredProvider(): void
    {
        $config = $this->createMock(SearchIndexConfig::class);
        $config->method('isConfigured')->willReturn(false);
        $runner = new SearchIndexingRunner(
            new FakeWpService(['__' => static fn(string $text): string => $text]),
            $config,
            $this->createMock(PostIndexerFactoryInterface::class),
            $this->createMock(SearchIndexingLockInterface::class),
            $this->createStub(ProgressReporterInterface::class),
        );

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('The search provider must be configured before indexing.');

        $runner->run('owner');
    }

    /**
     * Create a progress reporter that records messages and percentages.
     *
     * @param array<int, string> $messages
     * @param array<int, int|float> $percentages
     */
    private function createProgressReporter(array &$messages, array &$percentages): ProgressReporterInterface
    {
        return new class ($messages, $percentages) implements ProgressReporterInterface {
            /**
             * Create a recording progress reporter.
             */
            public function __construct(
                private array &$messages,
                private array &$percentages,
            ) {}

            /** Start progress reporting. */
            public function start(): void {}

            /** Record a progress message. */
            public function setMessage(string $message): void
            {
                $this->messages[] = $message;
            }

            /** Record a progress percentage. */
            public function setPercentage(int|float $percentage): void
            {
                $this->percentages[] = $percentage;
            }

            /** Finish progress reporting. */
            public function finish(string $message): void {}
        };
    }
}