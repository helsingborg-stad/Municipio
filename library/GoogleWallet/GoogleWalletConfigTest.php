<?php

declare(strict_types=1);

namespace Municipio\GoogleWallet;

use PHPUnit\Framework\TestCase;

/** Tests Google Wallet shared configuration checks. */
class GoogleWalletConfigTest extends TestCase
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

    /** Verifies that the service-account private key is read from the configured file. */
    public function testReadsPrivateKeyFromFile(): void
    {
        $config = new GoogleWalletConfig('123456789', 'wallet@example.com', $this->keyPath);

        static::assertSame("-----BEGIN PRIVATE KEY-----\nabc\n-----END PRIVATE KEY-----\n", $config->getServiceAccountPrivateKey());
    }

    /** Verifies that complete shared settings with a readable private key are accepted. */
    public function testIsConfiguredWithValidSettings(): void
    {
        static::assertTrue((new GoogleWalletConfig('123456789', 'wallet@example.com', $this->keyPath))->hasConfiguration());
    }

    /** Verifies that a non-numeric issuer ID is rejected. */
    public function testIsNotConfiguredWithInvalidIssuerId(): void
    {
        static::assertFalse((new GoogleWalletConfig('not-a-number', 'wallet@example.com', $this->keyPath))->hasConfiguration());
    }

    /** Verifies that a missing service-account email is rejected. */
    public function testIsNotConfiguredWithoutServiceAccountEmail(): void
    {
        static::assertFalse((new GoogleWalletConfig('123456789', '', $this->keyPath))->hasConfiguration());
    }

    /** Verifies that a missing private key file is rejected without a key. */
    public function testIsNotConfiguredWhenPrivateKeyFileIsMissing(): void
    {
        $config = new GoogleWalletConfig('123456789', 'wallet@example.com', '/missing/private-key.pem');

        static::assertFalse($config->hasConfiguration());
        static::assertSame('', $config->getServiceAccountPrivateKey());
    }

    /** Verifies that a file without a PEM private key is rejected. */
    public function testIsNotConfiguredWhenFileIsNotPrivateKey(): void
    {
        file_put_contents($this->keyPath, 'not a key');

        static::assertFalse((new GoogleWalletConfig('123456789', 'wallet@example.com', $this->keyPath))->hasConfiguration());
    }
}
