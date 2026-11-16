<?php

declare(strict_types=1);

namespace Municipio\Kulturkortet\Wallet;

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

        $timestamp = strtotime($date);
        return $timestamp === false ? null : date('Y-m-d', $timestamp);
    }
}
