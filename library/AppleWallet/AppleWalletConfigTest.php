<?php

declare(strict_types=1);

namespace Municipio\AppleWallet;

use PHPUnit\Framework\TestCase;

/** Tests Apple Wallet shared configuration checks. */
class AppleWalletConfigTest extends TestCase
{
    /** Verifies that complete shared settings with an existing directory and WWDR certificate are configured. */
    public function testIsConfiguredWithCompleteSharedSettings(): void
    {
        // Any existing file or directory satisfies the checks, so this test file stands in for the certificates.
        static::assertTrue((new AppleWalletConfig('TEAM12345', __DIR__, __FILE__))->hasConfiguration());
    }

    /** Verifies that a missing team identifier prevents signing. */
    public function testIsNotConfiguredWithoutTeamIdentifier(): void
    {
        static::assertFalse((new AppleWalletConfig('', __DIR__, __FILE__))->hasConfiguration());
    }

    /** Verifies that a missing certificate directory prevents signing. */
    public function testIsNotConfiguredWithoutCertificateDirectory(): void
    {
        static::assertFalse((new AppleWalletConfig('TEAM12345', '/missing/directory', __FILE__))->hasConfiguration());
    }

    /** Verifies that a missing WWDR certificate prevents signing. */
    public function testIsNotConfiguredWithoutWwdrCertificate(): void
    {
        static::assertFalse((new AppleWalletConfig('TEAM12345', __DIR__, '/missing/wwdr.pem'))->hasConfiguration());
    }
}
