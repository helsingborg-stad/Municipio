<?php

declare(strict_types=1);

namespace Municipio\GoogleWallet;

use PHPUnit\Framework\TestCase;

/** Tests Google Wallet card configuration checks. */
class GoogleWalletCardConfigTest extends TestCase
{
    private string $keyPath;

    /** Creates a temporary private key file. */
    protected function setUp(): void
    {
        parent::setUp();
        $this->keyPath = tempnam(sys_get_temp_dir(), 'google-wallet-key-');
        file_put_contents($this->keyPath, "-----BEGIN PRIVATE KEY-----\nabc\n-----END PRIVATE KEY-----\n");
    }

    /** Removes the temporary private key file. */
    protected function tearDown(): void
    {
        unlink($this->keyPath);
        parent::tearDown();
    }

    /** Verifies that an enabled card with a valid class suffix and shared settings is configured. */
    public function testIsConfiguredWhenEnabledWithValidClassSuffix(): void
    {
        static::assertTrue($this->createCard(true, 'kulturkortet')->hasConfiguration());
    }

    /** Verifies that a disabled card is not configured even when all settings are present. */
    public function testIsNotConfiguredWhenDisabled(): void
    {
        static::assertFalse($this->createCard(false, 'kulturkortet')->hasConfiguration());
    }

    /** Verifies that a class suffix with unsupported characters is rejected. */
    public function testIsNotConfiguredWithInvalidClassSuffix(): void
    {
        static::assertFalse($this->createCard(true, 'not valid!')->hasConfiguration());
    }

    /** Verifies that the issuer ID comes from the shared settings. */
    public function testExposesSharedIssuerId(): void
    {
        static::assertSame('123456789', $this->createCard(true, 'kulturkortet')->getIssuerId());
    }

    /**
     * @param bool $enabled Whether the card is enabled.
     * @param string $classSuffix Class suffix of the card.
     * @return GoogleWalletCardConfig A card using valid shared settings.
     */
    private function createCard(bool $enabled, string $classSuffix): GoogleWalletCardConfig
    {
        return new GoogleWalletCardConfig(
            new GoogleWalletConfig('123456789', 'wallet@example.com', $this->keyPath),
            $enabled,
            $classSuffix,
        );
    }
}
