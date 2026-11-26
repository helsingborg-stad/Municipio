<?php

declare(strict_types=1);

namespace Municipio\GoogleWallet;

/** Google Wallet settings of one card: its class within the shared issuer. */
readonly class GoogleWalletCardConfig
{
    /**
     * @param GoogleWalletConfig $googleWalletConfig Settings shared by all cards.
     * @param bool $enabled Whether this card is enabled.
     * @param string $classSuffix Suffix of the card's existing Google Wallet class.
     */
    public function __construct(
        private GoogleWalletConfig $googleWalletConfig,
        private bool $enabled,
        private string $classSuffix,
    ) {}

    /** @return string The Google Wallet issuer ID, from the shared settings. */
    public function getIssuerId(): string
    {
        return $this->googleWalletConfig->getIssuerId();
    }

    /** @return string The Google Wallet class suffix of the card. */
    public function getClassSuffix(): string
    {
        return $this->classSuffix;
    }

    /** @return string The service-account email, from the shared settings. */
    public function getServiceAccountEmail(): string
    {
        return $this->googleWalletConfig->getServiceAccountEmail();
    }

    /** @return string The service-account private key, from the shared settings. */
    public function getServiceAccountPrivateKey(): string
    {
        return $this->googleWalletConfig->getServiceAccountPrivateKey();
    }

    /** @return bool Whether the card is enabled and all required settings are available. */
    public function hasConfiguration(): bool
    {
        return $this->enabled
            && $this->googleWalletConfig->hasConfiguration()
            && preg_match('/^[A-Za-z0-9_-]+$/', $this->classSuffix) === 1;
    }
}
