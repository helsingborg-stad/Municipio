<?php

declare(strict_types=1);

namespace Municipio\Kulturkortet\Wallet;

use Municipio\AppleWallet\ApplePassCardConfig;
use Municipio\AppleWallet\ApplePassDataStore;
use Municipio\Kulturkortet\MunicipioAuth\navigation\MunicipioAuthNavigationInterface;
use RuntimeException;

/** Creates short-lived Apple pass URLs. */
class WalletService
{
    public const APPLE_PASS_QUERY_VAR = 'kulturkortet_apple_pass';
    public const APPLE_PASS_EXPIRED_QUERY_VAR = 'kulturkortet_apple_pass_expired';
    public const APPLE_PASS_TRANSIENT_PREFIX = 'kulturkortet_apple_pass_';
    private const TOKEN_EXPIRATION = 300;

    /**
     * @param ApplePassCardConfig $config Apple Wallet settings of the pass card.
     * @param ApplePassDataStore $applePassDataStore Stores temporary pass data behind one-time tokens.
     */
    public function __construct(
        private ApplePassCardConfig $config,
        private ApplePassDataStore $applePassDataStore,
    ) {}

    /**
     * Creates a one-time URL for an Apple pass.
     *
     * @param array $ticket Active Vitec ticket data.
     * @param MunicipioAuthNavigationInterface $navigation Builds the current-page URL.
     * @return string|null The pass URL, or null when Apple Wallet is unavailable.
     * @throws RuntimeException When the temporary pass data cannot be stored.
     */
    public function createApplePassUrl(array $ticket, MunicipioAuthNavigationInterface $navigation): ?string
    {
        $barcode = $ticket['barcode'] ?? null;
        if (!$this->config->hasConfiguration() || !is_string($barcode) || $barcode === '') {
            return null;
        }

        $token = $this->applePassDataStore->store([
            'id' => $ticket['id'] ?? $barcode,
            'firstname' => is_string($ticket['firstname'] ?? null) ? $ticket['firstname'] : '',
            'lastname' => is_string($ticket['lastname'] ?? null) ? $ticket['lastname'] : '',
            'validFrom' => is_string($ticket['validFrom'] ?? null) ? $ticket['validFrom'] : '',
            'validUntil' => is_string($ticket['validUntil'] ?? null) ? $ticket['validUntil'] : '',
            'barcode' => $barcode,
        ], self::TOKEN_EXPIRATION);

        return $navigation->getModifiedHomeUrl(
            removeQueryArgs: [self::APPLE_PASS_EXPIRED_QUERY_VAR],
            addQueryArgs: [self::APPLE_PASS_QUERY_VAR => $token],
        );
    }
}
