<?php

declare(strict_types=1);

namespace Municipio\Kulturkortet\Wallet;

use Municipio\Kulturkortet\MunicipioAuth\navigation\MunicipioAuthNavigationInterface;
use PHPUnit\Framework\TestCase;

/** Tests Wallet-button model creation. */
class WalletButtonDataFactoryTest extends TestCase
{
    /** Verifies that each configured provider URL is included under its provider key. */
    public function testReturnsAvailableAppleAndGoogleWalletLinks(): void
    {
        $appleWalletService = $this->createMock(WalletService::class);
        $appleWalletService->method('createApplePassUrl')->willReturn('https://example.test/apple-pass');
        $googleWalletLinkFactory = $this->createMock(GoogleWalletLinkFactory::class);
        $googleWalletLinkFactory->method('createLink')->willReturn('https://pay.google.com/gp/v/save/token');

        $wallet = (new WalletButtonDataFactory($appleWalletService, $googleWalletLinkFactory))->create(
            ['barcode' => 'ticket-barcode'],
            $this->createMock(MunicipioAuthNavigationInterface::class),
        );

        static::assertSame([
            'apple' => 'https://example.test/apple-pass',
            'google' => 'https://pay.google.com/gp/v/save/token',
        ], $wallet);
    }
}
