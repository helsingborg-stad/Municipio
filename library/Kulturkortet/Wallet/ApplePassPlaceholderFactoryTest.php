<?php

declare(strict_types=1);

namespace Municipio\Kulturkortet\Wallet;

use PHPUnit\Framework\TestCase;
use RuntimeException;

/** Tests mapping of Vitec tickets to Apple Wallet placeholders. */
class ApplePassPlaceholderFactoryTest extends TestCase
{
    /** Verifies that Apple Wallet receives the exact expiration time and timezone. */
    public function testIncludesOriginalExpirationDateTime(): void
    {
        $placeholders = (new ApplePassPlaceholderFactory())->create(
            ['validUntil' => '2030-12-31T23:59:58+02:00'],
            'ticket-barcode',
        );

        static::assertSame('2030-12-31T23:59:58+02:00', $placeholders['{{giltigtTom_ISO8601}}']);
    }

    /** Verifies that card placeholders are filled from the ticket and the barcode is the card number. */
    public function testFillsCardPlaceholdersFromTicket(): void
    {
        $placeholders = (new ApplePassPlaceholderFactory())->create(
            ['firstname' => ' Ada ', 'lastname' => 'Lovelace', 'validUntil' => '2030-12-31T23:59:58+02:00'],
            'ticket-barcode',
        );

        static::assertSame([
            '{{kortnummer}}' => 'ticket-barcode',
            '{{fornamn}}' => 'Ada',
            '{{efternamn}}' => 'Lovelace',
            '{{giltigtTom_ISO8601}}' => '2030-12-31T23:59:58+02:00',
        ], $placeholders);
    }

    /** Verifies that a ticket without an expiration date is rejected. */
    public function testRejectsTicketsWithoutExpirationDate(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The ticket does not contain a valid expiration date.');

        (new ApplePassPlaceholderFactory())->create(['firstname' => 'Ada'], 'ticket-barcode');
    }
}
