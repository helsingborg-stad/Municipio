<?php

declare(strict_types=1);

namespace Municipio\AppleWallet;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use WpService\Implementations\FakeWpService;

/** Tests storage and atomic consumption of temporary Apple Wallet pass data. */
class ApplePassDataStoreTest extends TestCase
{
    private const TRANSIENT_PREFIX = 'example_pass_';

    /** Verifies that pass data is stored under the hash of a one-time token. */
    public function testStoresPassDataUnderHashedTokenKey(): void
    {
        $wpService = new FakeWpService(['setTransient' => true]);

        $token = (new ApplePassDataStore($wpService, self::TRANSIENT_PREFIX))->store(['barcode' => 'ticket-barcode'], 300);

        static::assertMatchesRegularExpression('/^[a-f0-9]{64}$/', $token);
        static::assertSame(self::TRANSIENT_PREFIX . hash('sha256', $token), $wpService->methodCalls['setTransient'][0][0]);
        static::assertSame(['barcode' => 'ticket-barcode'], $wpService->methodCalls['setTransient'][0][1]);
        static::assertSame(300, $wpService->methodCalls['setTransient'][0][2]);
    }

    /** Verifies that a failed storage write is reported instead of returning a token. */
    public function testThrowsWhenPassDataCannotBeStored(): void
    {
        $this->expectException(RuntimeException::class);

        (new ApplePassDataStore(new FakeWpService(['setTransient' => false]), self::TRANSIENT_PREFIX))->store([], 300);
    }

    /** Verifies that pass data is claimed and removed before it is returned. */
    public function testAtomicallyConsumesPassData(): void
    {
        $token = str_repeat('a', 64);
        $tokenHash = hash('sha256', $token);
        $passData = ['barcode' => 'ticket-barcode'];
        $wpService = new FakeWpService([
            'addOption' => true,
            'getTransient' => $passData,
            'deleteTransient' => true,
            'deleteOption' => true,
        ]);

        static::assertSame($passData, (new ApplePassDataStore($wpService, self::TRANSIENT_PREFIX))->consume($token));
        static::assertSame(
            self::TRANSIENT_PREFIX . $tokenHash,
            $wpService->methodCalls['getTransient'][0][0],
        );
        static::assertSame(
            self::TRANSIENT_PREFIX . 'claim_' . $tokenHash,
            $wpService->methodCalls['addOption'][0][0],
        );
        static::assertSame(
            self::TRANSIENT_PREFIX . $tokenHash,
            $wpService->methodCalls['deleteTransient'][0][0],
        );
    }

    /** Verifies that a token already claimed by another request cannot be consumed. */
    public function testRejectsAlreadyClaimedPassToken(): void
    {
        $wpService = new FakeWpService(['addOption' => false]);

        static::assertNull((new ApplePassDataStore($wpService, self::TRANSIENT_PREFIX))->consume(str_repeat('b', 64)));
        static::assertArrayNotHasKey('getTransient', $wpService->methodCalls);
    }

    /** Verifies that a failed transient deletion releases the claim and rejects the request. */
    public function testReleasesClaimWhenTransientDeletionFails(): void
    {
        $wpService = new FakeWpService([
            'addOption' => true,
            'getTransient' => ['barcode' => 'ticket-barcode'],
            'deleteTransient' => false,
            'deleteOption' => true,
        ]);

        static::assertNull((new ApplePassDataStore($wpService, self::TRANSIENT_PREFIX))->consume(str_repeat('c', 64)));
        static::assertCount(1, $wpService->methodCalls['deleteOption']);
        static::assertSame(
            self::TRANSIENT_PREFIX . 'claim_' . hash('sha256', str_repeat('c', 64)),
            $wpService->methodCalls['deleteOption'][0][0],
        );
    }
}
