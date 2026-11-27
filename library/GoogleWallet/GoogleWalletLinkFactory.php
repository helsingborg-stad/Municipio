<?php

declare(strict_types=1);

namespace Municipio\GoogleWallet;

use Firebase\JWT\JWT;

/** Creates signed Google Wallet save links for generic objects in the configured class. */
class GoogleWalletLinkFactory
{
    /**
     * @param GoogleWalletCardConfig $config Wallet card settings.
     */
    public function __construct(private GoogleWalletCardConfig $config) {}

    /**
     * Creates a Google Wallet save link when the integration is enabled and configured.
     *
     * @param string $objectSuffix Suffix appended to the issuer ID to identify the object.
     * @param array $genericObject Generic object fields, such as cardTitle, header, textModulesData and barcode.
     * @return string|null The Google Wallet save URL, or null when unavailable.
     */
    public function createLink(string $objectSuffix, array $genericObject): ?string
    {
        if (!$this->config->hasConfiguration()) {
            return null;
        }

        $issuerId = $this->config->getIssuerId();
        $payload = [
            'iss' => $this->config->getServiceAccountEmail(),
            'aud' => 'google',
            'typ' => 'savetowallet',
            'iat' => time(),
            'payload' => [
                'genericObjects' => [[
                    'id' => $issuerId . '.' . $objectSuffix,
                    'classId' => $issuerId . '.' . $this->config->getClassSuffix(),
                    'state' => 'ACTIVE',
                    ...$genericObject,
                ]],
            ],
        ];

        $token = JWT::encode($payload, $this->config->getServiceAccountPrivateKey(), 'RS256');

        return 'https://pay.google.com/gp/v/save/' . $token;
    }
}
