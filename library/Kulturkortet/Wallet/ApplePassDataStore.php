<?php

declare(strict_types=1);

namespace Municipio\Kulturkortet\Wallet;

use SensitiveParameter;
use WpService\Contracts\AddOption;
use WpService\Contracts\DeleteOption;
use WpService\WpService;

/** Stores and atomically consumes temporary Apple pass data. */
class ApplePassDataStore
{
    private const CLAIM_PREFIX = 'kulturkortet_apple_pass_claim_';

    /**
     * @param WpService&AddOption&DeleteOption $wpService WordPress transient and option services.
     */
    public function __construct(private WpService&AddOption&DeleteOption $wpService) {}

    /**
     * Atomically consumes temporary pass data for a valid token.
     *
     * @param string $token One-time Apple pass token.
     * @return array|null Pass data, or null when unavailable or already claimed.
     */
    public function consume(#[SensitiveParameter] string $token): ?array
    {
        $tokenHash = hash('sha256', $token);
        $transientKey = WalletService::APPLE_PASS_TRANSIENT_PREFIX . $tokenHash;
        $claimKey = self::CLAIM_PREFIX . $tokenHash;

        if (!$this->wpService->addOption($claimKey, true, '', false)) {
            return null;
        }

        $passData = $this->wpService->getTransient($transientKey);
        if (!is_array($passData)) {
            $this->wpService->deleteOption($claimKey);
            return null;
        }

        if (!$this->wpService->deleteTransient($transientKey)) {
            return null;
        }

        $this->wpService->deleteOption($claimKey);

        return $passData;
    }
}
