<?php

declare(strict_types=1);

namespace Municipio\Kulturkortet\Wallet;

use DateTimeImmutable;
use Exception;

/** Formats ticket dates for Apple and Google Wallet payloads. */
class WalletDateFormatter
{
    /**
     * Converts a Vitec date value to the ISO date format used by Wallet passes.
     *
     * @param mixed $date Ticket date value.
     * @return string|null Formatted date, or null when the input is invalid.
     */
    public static function formatDate(mixed $date): ?string
    {
        if (!is_string($date) || $date === '') {
            return null;
        }

        return self::parseDate($date)?->format('Y-m-d');
    }

    /**
     * Converts a Vitec date value to the ISO date-time format used by Wallet passes.
     *
     * @param mixed $date Ticket date value.
     * @return string|null Formatted date-time, or null when the input is invalid.
     */
    public static function formatDateTime(mixed $date): ?string
    {
        return self::parseDate($date)?->format(DATE_ATOM);
    }

    /**
     * Parses a ticket date without changing its source timezone.
     *
     * @param mixed $date Ticket date value.
     * @return DateTimeImmutable|null Parsed date, or null when the input is invalid.
     */
    private static function parseDate(mixed $date): ?DateTimeImmutable
    {
        if (!is_string($date) || $date === '') {
            return null;
        }

        try {
            return new DateTimeImmutable($date);
        } catch (Exception) {
            return null;
        }
    }
}
