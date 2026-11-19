<?php

declare(strict_types=1);

namespace Municipio\Kulturkortet\Wallet;

use PHPUnit\Framework\TestCase;
use RuntimeException;

/** Tests Apple Wallet pass payload data. */
class ApplePassDataFactoryTest extends TestCase
{
    /** Verifies that Apple Wallet receives the exact expiration time and timezone. */
    public function testIncludesOriginalExpirationDateTime(): void
    {
        $passData = $this->createFactory()->create(
            ['validUntil' => '2030-12-31T23:59:58+02:00'],
            'ticket-barcode',
            'ordinarie',
        );

        static::assertSame('2030-12-31T23:59:58+02:00', $passData['expirationDate']);
    }

    /** Verifies that template placeholders are filled from the ticket and the barcode is the card number. */
    public function testFillsTemplatePlaceholdersFromTicket(): void
    {
        $passData = $this->createFactory()->create(
            ['firstname' => ' Ada ', 'lastname' => 'Lovelace', 'validUntil' => '2030-12-31T23:59:58+02:00'],
            'ticket-barcode',
            'ordinarie',
        );

        static::assertSame('ticket-barcode', $passData['serialNumber']);
        static::assertSame('TEAM12345', $passData['teamIdentifier']);
        static::assertSame('pass.example.kulturkortet', $passData['passTypeIdentifier']);
        static::assertSame('Ada Lovelace', $passData['storeCard']['auxiliaryFields'][0]['value']);
        static::assertSame('2030-12-31T23:59:58+02:00', $passData['storeCard']['headerFields'][0]['value']);
        static::assertStringNotContainsString('{{', json_encode($passData, JSON_THROW_ON_ERROR));
    }

    /** Verifies that a ticket without an expiration date is rejected. */
    public function testRejectsTicketsWithoutExpirationDate(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The ticket does not contain a valid expiration date.');

        $this->createFactory()->create(['firstname' => 'Ada'], 'ticket-barcode', 'ordinarie');
    }

    /** @return ApplePassDataFactory A factory using the bundled templates and a fixed Apple configuration. */
    private function createFactory(): ApplePassDataFactory
    {
        $config = $this->createMock(AppleWalletConfig::class);
        $config->method('getPassTypeIdentifier')->willReturn('pass.example.kulturkortet');
        $config->method('getTeamIdentifier')->willReturn('TEAM12345');

        return new ApplePassDataFactory($config, new ApplePassTemplateLoader());
    }
}
