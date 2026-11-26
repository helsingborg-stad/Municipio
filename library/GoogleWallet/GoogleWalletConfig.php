<?php

declare(strict_types=1);

namespace Municipio\GoogleWallet;

/** Google Wallet settings shared by every card of a site: the issuer and the service account. */
readonly class GoogleWalletConfig
{
    /**
     * @param string $issuerId Google Wallet issuer ID, digits only.
     * @param string $serviceAccountEmail Service-account email used to sign Google Wallet JWTs.
     * @param string $serviceAccountPrivateKeyPath Local path to the service account's private key in PEM format.
     */
    public function __construct(
        private string $issuerId,
        private string $serviceAccountEmail,
        private string $serviceAccountPrivateKeyPath,
    ) {}

    /** @return string The Google Wallet issuer ID. */
    public function getIssuerId(): string
    {
        return $this->issuerId;
    }

    /** @return string The service-account email used to sign Google Wallet JWTs. */
    public function getServiceAccountEmail(): string
    {
        return $this->serviceAccountEmail;
    }

    /** @return string The service-account private key in PEM format, or an empty string when the file cannot be read. */
    public function getServiceAccountPrivateKey(): string
    {
        if (!is_file($this->serviceAccountPrivateKeyPath) || !is_readable($this->serviceAccountPrivateKeyPath)) {
            return '';
        }

        return (string) file_get_contents($this->serviceAccountPrivateKeyPath);
    }

    /** @return bool Whether the shared settings are complete and valid. */
    public function hasConfiguration(): bool
    {
        return ctype_digit($this->issuerId)
            && filter_var($this->serviceAccountEmail, FILTER_VALIDATE_EMAIL) !== false
            && str_contains($this->getServiceAccountPrivateKey(), 'PRIVATE KEY');
    }
}
