<?php

declare(strict_types=1);

namespace Municipio\Kulturkortet\Wallet;

use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

/** Tests atomic consumption of temporary Apple Wallet pass data. */
class ApplePassDataStoreTest extends TestCase
{
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

        static::assertSame($passData, (new ApplePassDataStore($wpService))->consume($token));
        static::assertSame(
            WalletService::APPLE_PASS_TRANSIENT_PREFIX . $tokenHash,
            $wpService->methodCalls['getTransient'][0][0],
        );
        static::assertSame(
            'kulturkortet_apple_pass_claim_' . $tokenHash,
            $wpService->methodCalls['addOption'][0][0],
        );
        static::assertSame(
            WalletService::APPLE_PASS_TRANSIENT_PREFIX . $tokenHash,
            $wpService->methodCalls['deleteTransient'][0][0],
        );
    }

    /** Verifies that a token already claimed by another request cannot be consumed. */
    public function testRejectsAlreadyClaimedPassToken(): void
    {
        $wpService = new FakeWpService(['addOption' => false]);

        static::assertNull((new ApplePassDataStore($wpService))->consume(str_repeat('b', 64)));
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

        static::assertNull((new ApplePassDataStore($wpService))->consume(str_repeat('c', 64)));
        static::assertCount(1, $wpService->methodCalls['deleteOption']);
        static::assertSame(
            'kulturkortet_apple_pass_claim_' . hash('sha256', str_repeat('c', 64)),
            $wpService->methodCalls['deleteOption'][0][0],
        );
    }
}
