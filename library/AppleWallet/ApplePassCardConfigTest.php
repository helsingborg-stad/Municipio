<?php

declare(strict_types=1);

namespace Municipio\AppleWallet;

use PHPUnit\Framework\TestCase;

/** Tests Apple Wallet pass card configuration checks and certificate resolution. */
class ApplePassCardConfigTest extends TestCase
{
    /** Verifies that the certificate is resolved inside the shared certificate directory. */
    public function testResolvesCertificateInsideSharedDirectory(): void
    {
        $card = new ApplePassCardConfig($this->createSharedConfig(), true, 'pass.example.test', 'signing.p12', 'secret');

        static::assertSame(__DIR__ . '/signing.p12', $card->getCertificatePath());
    }

    /** Verifies that a stored full path is reduced to its file name inside the shared directory. */
    public function testUsesOnlyFileNameOfCertificate(): void
    {
        $card = new ApplePassCardConfig($this->createSharedConfig(), true, 'pass.example.test', '/old/location/' . basename(__FILE__), '');

        static::assertSame(__DIR__ . '/' . basename(__FILE__), $card->getCertificatePath());
    }

    /** Verifies that shared settings are exposed through the card. */
    public function testExposesSharedSettings(): void
    {
        $card = new ApplePassCardConfig($this->createSharedConfig(), true, 'pass.example.test', 'signing.p12', '');

        static::assertSame('TEAM12345', $card->getTeamIdentifier());
        static::assertSame(__FILE__, $card->getWwdrCertificatePath());
    }

    /** Verifies that an enabled card with all settings and a readable certificate is configured. */
    public function testIsConfiguredWhenEnabledWithReadableCertificate(): void
    {
        $card = new ApplePassCardConfig($this->createSharedConfig(), true, 'pass.example.test', basename(__FILE__), '');

        static::assertTrue($card->hasConfiguration());
    }

    /** Verifies that a disabled card is not configured even when all settings are present. */
    public function testIsNotConfiguredWhenDisabled(): void
    {
        $card = new ApplePassCardConfig($this->createSharedConfig(), false, 'pass.example.test', basename(__FILE__), '');

        static::assertFalse($card->hasConfiguration());
    }

    /** Verifies that a card without a Pass Type ID is not configured. */
    public function testIsNotConfiguredWithoutPassTypeIdentifier(): void
    {
        $card = new ApplePassCardConfig($this->createSharedConfig(), true, '', basename(__FILE__), '');

        static::assertFalse($card->hasConfiguration());
    }

    /** Verifies that a missing certificate file prevents signing. */
    public function testIsNotConfiguredWithoutCertificateFile(): void
    {
        $card = new ApplePassCardConfig($this->createSharedConfig(), true, 'pass.example.test', 'missing.p12', '');

        static::assertFalse($card->hasConfiguration());
    }

    /** Verifies that a card is not configured when the shared settings are missing. */
    public function testIsNotConfiguredWithoutSharedSettings(): void
    {
        $card = new ApplePassCardConfig(new AppleWalletConfig('', __DIR__, __FILE__), true, 'pass.example.test', basename(__FILE__), '');

        static::assertFalse($card->hasConfiguration());
    }

    /** @return AppleWalletConfig Shared settings whose directory and WWDR certificate exist. */
    private function createSharedConfig(): AppleWalletConfig
    {
        return new AppleWalletConfig('TEAM12345', __DIR__, __FILE__);
    }
}
