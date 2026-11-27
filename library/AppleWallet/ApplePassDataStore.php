<?php

declare(strict_types=1);

namespace Municipio\AppleWallet;

use RuntimeException;
use SensitiveParameter;
use WpService\Contracts\AddOption;
use WpService\Contracts\DeleteOption;
use WpService\WpService;

/** Stores temporary Apple pass data behind one-time tokens and atomically consumes it. */
class ApplePassDataStore
{
    /**
     * @param WpService&AddOption&DeleteOption $wpService WordPress transient and option services.
     * @param string $transientPrefix Prefix for transient keys, followed by the hash of the token.
     */
    public function __construct(
        private WpService&AddOption&DeleteOption $wpService,
        private string $transientPrefix,
    ) {}

    /**
     * Stores pass data and returns a one-time token for it.
     *
     * @param array $passData Pass data to store.
     * @param int $expiration Lifetime of the stored data in seconds.
     * @return string One-time token.
     * @throws RuntimeException When the pass data cannot be stored.
     */
    public function store(array $passData, int $expiration): string
    {
        $token = bin2hex(random_bytes(32));
        if (!$this->wpService->setTransient($this->getTransientKey($token), $passData, $expiration)) {
            throw new RuntimeException('Unable to store the Apple Wallet pass data.');
        }

        return $token;
    }

    /**
     * Atomically consumes temporary pass data for a valid token.
     *
     * @param string $token One-time Apple pass token.
     * @return array|null Pass data, or null when unavailable or already claimed.
     */
    public function consume(#[SensitiveParameter] string $token): ?array
    {
        $transientKey = $this->getTransientKey($token);
        $claimKey = $this->transientPrefix . 'claim_' . hash('sha256', $token);

        if (!$this->wpService->addOption($claimKey, true, '', false)) {
            return null;
        }

        $passData = $this->wpService->getTransient($transientKey);
        if (!is_array($passData)) {
            $this->wpService->deleteOption($claimKey);
            return null;
        }

        if (!$this->wpService->deleteTransient($transientKey)) {
            $this->wpService->deleteOption($claimKey);
            return null;
        }

        $this->wpService->deleteOption($claimKey);

        return $passData;
    }

    /**
     * Returns the transient key for a token.
     *
     * @param string $token One-time Apple pass token.
     * @return string Transient key.
     */
    private function getTransientKey(#[SensitiveParameter] string $token): string
    {
        return $this->transientPrefix . hash('sha256', $token);
    }
}
