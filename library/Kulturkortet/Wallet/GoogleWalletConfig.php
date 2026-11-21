<?php

declare(strict_types=1);

namespace Municipio\Kulturkortet\Wallet;

use AcfService\Contracts\GetField;

/** Reads Google Wallet settings from the Kulturkortet options page. */
class GoogleWalletConfig
{
    /**
     * @param GetField $acfService ACF field reader.
     */
    public function __construct(private GetField $acfService) {}

    /** @return string The configured Google Wallet issuer ID. */
    public function getIssuerId(): string
    {
        return $this->getString('kulturkortet_wallet_google_issuer_id');
    }

    /** @return string The configured Google Wallet class suffix. */
    public function getClassSuffix(): string
    {
        return $this->getString('kulturkortet_wallet_google_class_suffix');
    }

    /** @return string The service-account email used to sign Google Wallet JWTs. */
    public function getServiceAccountEmail(): string
    {
        return $this->getString('kulturkortet_wallet_google_service_account_email');
    }

    /** @return string The normalized Google Wallet service-account private key. */
    public function getServiceAccountPrivateKey(): string
    {
        return str_replace('\\n', "\n", $this->getString('kulturkortet_wallet_google_service_account_private_key'));
    }

    /** @return bool Whether Google Wallet is enabled and all required settings are available. */
    public function hasConfiguration(): bool
    {
        return (bool) $this->acfService->getField('kulturkortet_wallet_google_enabled', 'option')
            && ctype_digit($this->getIssuerId())
            && preg_match('/^[A-Za-z0-9_-]+$/', $this->getClassSuffix()) === 1
            && filter_var($this->getServiceAccountEmail(), FILTER_VALIDATE_EMAIL) !== false
            && str_contains($this->getServiceAccountPrivateKey(), 'PRIVATE KEY');
    }

    /**
     * Reads a string option.
     *
     * @param string $fieldName ACF field name.
     * @return string The trimmed option value, or an empty string.
     */
    private function getString(string $fieldName): string
    {
        $value = $this->acfService->getField($fieldName, 'option');
        return is_string($value) ? trim($value) : '';
    }
}
