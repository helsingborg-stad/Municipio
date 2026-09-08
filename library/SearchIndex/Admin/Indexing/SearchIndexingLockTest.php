<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Admin\Indexing;

use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

/**
 * Tests exclusive Search Index admin indexing locks.
 */
class SearchIndexingLockTest extends TestCase
{
    /**
     * Verify a new lock is acquired atomically.
     */
    public function testAcquiresAvailableLock(): void
    {
        $wpService = new FakeWpService(['addOption' => true]);

        $acquired = (new SearchIndexingLock($wpService))->acquire('owner-one');

        static::assertTrue($acquired);
        static::assertSame('municipio_search_index_admin_indexing_lock', $wpService->methodCalls['addOption'][0][0]);
        static::assertSame('owner-one', $wpService->methodCalls['addOption'][0][1]['owner']);
    }

    /**
     * Verify an active lock prevents another indexing run.
     */
    public function testRejectsConcurrentLockOwner(): void
    {
        $wpService = new FakeWpService([
            'addOption' => false,
            'getOption' => ['owner' => 'owner-one', 'expiresAt' => time() + 60],
        ]);

        $acquired = (new SearchIndexingLock($wpService))->acquire('owner-two');

        static::assertFalse($acquired);
        static::assertArrayNotHasKey('deleteOption', $wpService->methodCalls);
    }

    /**
     * Verify a stale lock can be replaced by one new owner.
     */
    public function testReclaimsExpiredLock(): void
    {
        $attempt = 0;
        $wpService = new FakeWpService([
            'addOption' => static function () use (&$attempt): bool {
                $attempt++;
                return $attempt === 2;
            },
            'getOption' => ['owner' => 'owner-one', 'expiresAt' => time() - 1],
            'deleteOption' => true,
        ]);

        $acquired = (new SearchIndexingLock($wpService))->acquire('owner-two');

        static::assertTrue($acquired);
        static::assertCount(2, $wpService->methodCalls['addOption']);
        static::assertCount(1, $wpService->methodCalls['deleteOption']);
    }

    /**
     * Verify one request cannot release another request's lock.
     */
    public function testOnlyOwnerCanReleaseLock(): void
    {
        $wpService = new FakeWpService([
            'getOption' => ['owner' => 'owner-one', 'expiresAt' => time() + 60],
        ]);

        (new SearchIndexingLock($wpService))->release('owner-two');

        static::assertArrayNotHasKey('deleteOption', $wpService->methodCalls);
    }
}