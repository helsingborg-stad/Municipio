<?php

declare(strict_types=1);

namespace Municipio\Kulturkortet\Wallet;

use PHPUnit\Framework\TestCase;

/** Tests Apple Wallet pass payload data. */
class ApplePassDataFactoryTest extends TestCase
{
    /** Verifies that Apple Wallet receives the exact expiration time and timezone. */
    public function testIncludesOriginalExpirationDateTime(): void
    {
        $config = $this->createMock(AppleWalletConfig::class);
        $config->method('getPassTypeIdentifier')->willReturn('pass.example.kulturkortet');
        $config->method('getTeamIdentifier')->willReturn('TEAM12345');

        $passData = (new ApplePassDataFactory($config))->create(
            ['validUntil' => '2030-12-31T23:59:58+02:00'],
            'ticket-barcode',
        );

        static::assertSame('2030-12-31T23:59:58+02:00', $passData['expirationDate']);
    }

    /** Verifies that an empty ticket ID falls back to the unique barcode for the pass serial. */
    public function testUsesBarcodeWhenTicketIdIsEmpty(): void
    {
        $config = $this->createMock(AppleWalletConfig::class);
        $config->method('getPassTypeIdentifier')->willReturn('pass.example.kulturkortet');
        $config->method('getTeamIdentifier')->willReturn('TEAM12345');

        $passData = (new ApplePassDataFactory($config))->create(['id' => ''], 'ticket-barcode');

        static::assertSame(substr(hash('sha256', 'ticket-barcode'), 0, 40), $passData['serialNumber']);
    }
}
