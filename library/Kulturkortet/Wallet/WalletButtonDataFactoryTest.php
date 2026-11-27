<?php

declare(strict_types=1);

namespace Municipio\Kulturkortet\Wallet;

use Municipio\AppleWallet\ApplePassDataStore;
use Municipio\AppleWallet\ApplePassCardConfig;
use Municipio\GoogleWallet\GoogleWalletCardConfig;
use Municipio\GoogleWallet\GoogleWalletLinkFactory;
use Municipio\Kulturkortet\MunicipioAuth\navigation\MunicipioAuthNavigationInterface;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

/** Tests Wallet-button model creation. */
class WalletButtonDataFactoryTest extends TestCase
{
    /** Verifies that each configured provider URL is included under its provider key. */
    public function testReturnsAvailableAppleAndGoogleWalletLinks(): void
    {
        $appleWalletService = $this->createMock(WalletService::class);
        $appleWalletService->method('createApplePassUrl')->willReturn('https://example.test/apple-pass');
        $googleWalletLinkFactory = $this->createMock(TicketGoogleWalletLinkFactory::class);
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

    /** Verifies that no Wallet button is offered when neither provider has its settings, so the UI hides both. */
    public function testReturnsNoWalletLinksWhenNeitherProviderIsConfigured(): void
    {
        $appleConfig = $this->createMock(ApplePassCardConfig::class);
        $appleConfig->method('hasConfiguration')->willReturn(false);
        $googleConfig = $this->createMock(GoogleWalletCardConfig::class);
        $googleConfig->method('hasConfiguration')->willReturn(false);
        $appleWalletService = new WalletService($appleConfig, new ApplePassDataStore(new FakeWpService(), WalletService::APPLE_PASS_TRANSIENT_PREFIX));
        $googleWalletLinkFactory = new TicketGoogleWalletLinkFactory(new GoogleWalletLinkFactory($googleConfig));

        $wallet = (new WalletButtonDataFactory($appleWalletService, $googleWalletLinkFactory))->create(
            ['id' => 'ticket-1', 'barcode' => 'ticket-barcode', 'validUntil' => '2030-12-31'],
            $this->createMock(MunicipioAuthNavigationInterface::class),
        );

        static::assertSame([], $wallet);
    }
}
