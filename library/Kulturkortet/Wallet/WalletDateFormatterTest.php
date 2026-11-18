<?php

declare(strict_types=1);

namespace Municipio\Kulturkortet\Wallet;

use PHPUnit\Framework\TestCase;

/** Tests source-timezone-preserving wallet date formatting. */
class WalletDateFormatterTest extends TestCase
{
    /** Verifies that date-only values retain the calendar date in their source timezone. */
    public function testFormatsDateWithoutChangingSourceTimezone(): void
    {
        static::assertSame('2030-01-01', WalletDateFormatter::formatDate('2030-01-01T00:00:00+02:00'));
    }

    /** Verifies that invalid values do not produce wallet dates. */
    public function testReturnsNullForInvalidDate(): void
    {
        static::assertNull(WalletDateFormatter::formatDate('not-a-date'));
        static::assertNull(WalletDateFormatter::formatDateTime('not-a-date'));
    }
}
