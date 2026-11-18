<?php

declare(strict_types=1);

namespace Municipio\Kulturkortet\Wallet;

/** Builds Apple Wallet JSON data from an active Vitec ticket. */
class ApplePassDataFactory
{
    /**
     * @param AppleWalletConfig $config Apple pass identifiers.
     */
    public function __construct(private AppleWalletConfig $config) {}

    /**
     * Creates Apple Wallet payload data from the Vitec ticket.
     *
     * @param array $ticket Active Vitec ticket data.
     * @param string $barcode Ticket barcode.
     * @return array Pass data.
     */
    public function create(array $ticket, string $barcode): array
    {
        $ticketId = $ticket['id'] ?? null;
        $serialNumber = is_scalar($ticketId) && (string) $ticketId !== '' ? (string) $ticketId : $barcode;
        $expirationDate = WalletDateFormatter::formatDateTime($ticket['validUntil'] ?? null);

        return [
            'formatVersion' => 1,
            'passTypeIdentifier' => $this->config->getPassTypeIdentifier(),
            'serialNumber' => substr(hash('sha256', $serialNumber), 0, 40),
            'teamIdentifier' => $this->config->getTeamIdentifier(),
            ...($expirationDate !== null ? ['expirationDate' => $expirationDate] : []),
            'organizationName' => 'Kulturkortet',
            'description' => 'Kulturkortet',
            'storeCard' => [
                'primaryFields' => [[
                    'key' => 'cardholder',
                    'label' => 'KORTHAVARE',
                    'value' => trim(
                        (is_string($ticket['firstname'] ?? null) ? trim($ticket['firstname']) : '')
                        . ' '
                        . (is_string($ticket['lastname'] ?? null) ? trim($ticket['lastname']) : ''),
                    ),
                ]],
                'secondaryFields' => $this->getDateFields($ticket),
            ],
            'barcode' => [
                'message' => $barcode,
                'format' => 'PKBarcodeFormatQR',
                'messageEncoding' => 'iso-8859-1',
            ],
            'barcodes' => [[
                'message' => $barcode,
                'format' => 'PKBarcodeFormatQR',
                'messageEncoding' => 'iso-8859-1',
            ]],
        ];
    }

    /**
     * Returns pass fields for ticket dates that can be parsed.
     *
     * @param array $ticket Active Vitec ticket data.
     * @return array Formatted date fields.
     */
    private function getDateFields(array $ticket): array
    {
        $fields = [];
        foreach (['validFrom' => 'GILTIG FRÅN', 'validUntil' => 'GILTIG TILL'] as $key => $label) {
            $value = WalletDateFormatter::formatDate($ticket[$key] ?? null);
            if ($value !== null) {
                $fields[] = [
                    'key' => $key,
                    'label' => $label,
                    'value' => $value,
                ];
            }
        }

        return $fields;
    }
}
