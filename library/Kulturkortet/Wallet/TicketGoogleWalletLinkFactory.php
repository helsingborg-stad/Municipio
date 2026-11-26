<?php

declare(strict_types=1);

namespace Municipio\Kulturkortet\Wallet;

use Municipio\GoogleWallet\GoogleWalletLinkFactory;

/** Creates Kulturkortet Google Wallet save links for active Vitec tickets. */
class TicketGoogleWalletLinkFactory
{
    /**
     * @param GoogleWalletLinkFactory $googleWalletLinkFactory Creates signed Google Wallet save links.
     */
    public function __construct(private GoogleWalletLinkFactory $googleWalletLinkFactory) {}

    /**
     * Creates a Google Wallet link for a ticket when the integration is enabled and configured.
     *
     * @param array $ticket Active Vitec ticket data.
     * @return string|null The Google Wallet save URL, or null when unavailable.
     */
    public function createLink(array $ticket): ?string
    {
        $barcode = $ticket['barcode'] ?? null;
        if (!is_string($barcode) || $barcode === '') {
            return null;
        }

        $ticketId = $ticket['id'] ?? null;
        $objectSuffix = substr(hash('sha256', (is_scalar($ticketId) ? (string) $ticketId : '') . "\0" . $barcode), 0, 40);
        $firstName = is_string($ticket['firstname'] ?? null) ? trim($ticket['firstname']) : '';
        $lastName = is_string($ticket['lastname'] ?? null) ? trim($ticket['lastname']) : '';
        $fullName = trim($firstName . ' ' . $lastName);
        $expiryDate = WalletDateFormatter::formatDate($ticket['validUntil'] ?? null);
        $expirationDateTime = WalletDateFormatter::formatDateTime($ticket['validUntil'] ?? null);

        return $this->googleWalletLinkFactory->createLink($objectSuffix, [
            ...($expirationDateTime === null ? [] : [
                'validTimeInterval' => [
                    'end' => ['dateTime' => $expirationDateTime],
                ],
            ]),
            'cardTitle' => [
                'defaultValue' => [
                    'language' => 'sv-SE',
                    'value' => 'Kulturkortet',
                ],
            ],
            'header' => [
                'defaultValue' => [
                    'language' => 'sv-SE',
                    'value' => $fullName !== '' ? $fullName : 'Kulturkortet',
                ],
            ],
            'textModulesData' => $expiryDate === null ? [] : [[
                'id' => 'valid_until',
                'header' => 'Giltigt till',
                'body' => $expiryDate,
            ]],
            'barcode' => [
                'type' => 'QR_CODE',
                'value' => $barcode,
            ],
        ]);
    }
}
