<?php

declare(strict_types=1);

namespace Municipio\Kulturkortet\Wallet;

use AcfService\Contracts\GetField;
use PHPUnit\Framework\TestCase;

/** Tests mapping of Kulturkortet Wallet settings to wallet configuration. */
class WalletSettingsTest extends TestCase
{
    /**
     * Verifies that card options are mapped with trimmed identifiers and an untrimmed password.
     *
     * @mago-expect lint:no-literal-password The array key is an ACF field name, and the value is a test fixture.
     */
    public function testMapsApplePassCardOptions(): void
    {
        $settings = new WalletSettings($this->createAcfService([
            'kulturkortet_wallet_apple_enabled' => true,
            'kulturkortet_wallet_apple_pass_type_identifier' => ' pass.example.test ',
            'kulturkortet_wallet_apple_certificate' => 'signing.p12',
            'kulturkortet_wallet_apple_certificate_password' => ' secret ',
        ]));

        $card = $settings->getApplePassCardConfig();

        static::assertSame('pass.example.test', $card->getPassTypeIdentifier());
        static::assertSame(' secret ', $card->getCertificatePassword());
    }

    /** Verifies that Google Wallet card options are mapped with trimmed values. */
    public function testMapsGoogleWalletCardOptions(): void
    {
        $settings = new WalletSettings($this->createAcfService([
            'kulturkortet_wallet_google_enabled' => true,
            'kulturkortet_wallet_google_class_suffix' => ' kulturkortet ',
        ]));

        static::assertSame('kulturkortet', $settings->getGoogleWalletCardConfig()->getClassSuffix());
    }

    /** Verifies that a card is not offered when the shared settings are not defined in wp-config.php. */
    public function testCardsAreNotConfiguredWhenSharedConstantsAreMissing(): void
    {
        $settings = new WalletSettings($this->createAcfService([
            'kulturkortet_wallet_apple_enabled' => true,
            'kulturkortet_wallet_apple_pass_type_identifier' => 'pass.example.test',
            'kulturkortet_wallet_apple_certificate' => 'signing.p12',
            'kulturkortet_wallet_google_enabled' => true,
            'kulturkortet_wallet_google_class_suffix' => 'kulturkortet',
        ]));

        static::assertFalse($settings->getApplePassCardConfig()->hasConfiguration());
        static::assertFalse($settings->getGoogleWalletCardConfig()->hasConfiguration());
    }

    /**
     * @param array<string, mixed> $values Option values keyed by ACF field name.
     * @return GetField An ACF reader returning the given option values.
     */
    private function createAcfService(array $values): GetField
    {
        $acfService = $this->createMock(GetField::class);
        $acfService->method('getField')->willReturnCallback(
            static fn(string $fieldName): mixed => $values[$fieldName] ?? null,
        );

        return $acfService;
    }
}
