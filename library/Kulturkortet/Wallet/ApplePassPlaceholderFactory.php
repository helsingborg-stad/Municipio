<?php

declare(strict_types=1);

namespace Municipio\Kulturkortet\Wallet;

use RuntimeException;

/** Maps an active Vitec ticket to the Kulturkortet Apple Wallet template placeholders. */
class ApplePassPlaceholderFactory
{
    /**
     * Creates the placeholder replacements for the Kulturkortet pass template.
     *
     * @param array $ticket Active Vitec ticket data.
     * @param string $barcode Ticket barcode, used as the card number.
     * @return array<string, string> Placeholder replacements keyed by placeholder.
     * @throws RuntimeException When the ticket has no usable expiration date.
     */
    public function create(array $ticket, string $barcode): array
    {
        $expirationDate = WalletDateFormatter::formatDateTime($ticket['validUntil'] ?? null);
        if ($expirationDate === null) {
            throw new RuntimeException('The ticket does not contain a valid expiration date.');
        }

        return [
            '{{kortnummer}}' => $barcode,
            '{{fornamn}}' => $this->getTicketString($ticket, 'firstname'),
            '{{efternamn}}' => $this->getTicketString($ticket, 'lastname'),
            '{{giltigtTom_ISO8601}}' => $expirationDate,
        ];
    }

    /**
     * Returns a trimmed string ticket value, or an empty string when it is missing.
     *
     * @param array $ticket Active Vitec ticket data.
     * @param string $key Ticket key.
     * @return string The trimmed value.
     */
    private function getTicketString(array $ticket, string $key): string
    {
        return is_string($ticket[$key] ?? null) ? trim($ticket[$key]) : '';
    }
}
