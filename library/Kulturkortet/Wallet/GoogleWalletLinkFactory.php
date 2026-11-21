<?php

declare(strict_types=1);

namespace Municipio\Kulturkortet\Wallet;

use Firebase\JWT\JWT;

/** Creates signed Google Wallet save links for Kulturkortet tickets. */
class GoogleWalletLinkFactory
{
    /**
     * @param GoogleWalletConfig $config Wallet issuer and signing settings.
     */
    public function __construct(private GoogleWalletConfig $config) {}

    /**
     * Creates a Google Wallet link when the integration is enabled and configured.
     *
     * @param array $ticket Active Vitec ticket data.
     * @return string|null The Google Wallet save URL, or null when unavailable.
     */
    public function createLink(array $ticket): ?string
    {
        $barcode = $ticket['barcode'] ?? null;

        if (!$this->config->hasConfiguration() || !is_string($barcode) || $barcode === '') {
            return null;
        }

        $issuerId = $this->config->getIssuerId();
        $classId = $issuerId . '.' . $this->config->getClassSuffix();
        $ticketId = $ticket['id'] ?? null;
        $objectSuffix = substr(hash('sha256', (is_scalar($ticketId) ? (string) $ticketId : '') . "\0" . $barcode), 0, 40);
        $firstName = is_string($ticket['firstname'] ?? null) ? trim($ticket['firstname']) : '';
        $lastName = is_string($ticket['lastname'] ?? null) ? trim($ticket['lastname']) : '';
        $fullName = trim($firstName . ' ' . $lastName);
        $expiryDate = WalletDateFormatter::formatDate($ticket['validUntil'] ?? null);
        $expirationDateTime = WalletDateFormatter::formatDateTime($ticket['validUntil'] ?? null);

        $payload = [
            'iss' => $this->config->getServiceAccountEmail(),
            'aud' => 'google',
            'typ' => 'savetowallet',
            'iat' => time(),
            'payload' => [
                'genericObjects' => [[
                    'id' => $issuerId . '.' . $objectSuffix,
                    'classId' => $classId,
                    'state' => 'ACTIVE',
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
                ]],
            ],
        ];

        $token = JWT::encode($payload, $this->config->getServiceAccountPrivateKey(), 'RS256');

        return 'https://pay.google.com/gp/v/save/' . $token;
    }
}
