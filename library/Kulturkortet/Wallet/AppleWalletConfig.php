<?php

declare(strict_types=1);

namespace Municipio\Kulturkortet\Wallet;

use AcfService\Contracts\GetField;

/** Reads Apple Wallet settings from the Kulturkortet options page. */
class AppleWalletConfig
{
    /**
     * @param GetField $acfService ACF field reader.
     */
    public function __construct(private GetField $acfService) {}

    /** @return string The configured Apple Pass Type ID. */
    public function getPassTypeIdentifier(): string
    {
        return $this->getString('kulturkortet_wallet_apple_pass_type_identifier');
    }

    /** @return string The configured Apple Team ID. */
    public function getTeamIdentifier(): string
    {
        return $this->getString('kulturkortet_wallet_apple_team_identifier');
    }

    /** @return string The local path to the Apple signing certificate. */
    public function getCertificatePath(): string
    {
        return $this->getString('kulturkortet_wallet_apple_certificate');
    }

    /** @return string The password for the Apple signing certificate. */
    public function getCertificatePassword(): string
    {
        $value = $this->acfService->getField('kulturkortet_wallet_apple_certificate_password', 'option');
        return is_string($value) ? $value : '';
    }

    /** @return string The local path to the Apple WWDR certificate. */
    public function getWwdrCertificatePath(): string
    {
        return $this->getString('kulturkortet_wallet_apple_wwdr_certificate');
    }

    /** @return bool Whether Apple Wallet is enabled and all required settings are available. */
    public function hasConfiguration(): bool
    {
        return (bool) $this->acfService->getField('kulturkortet_wallet_apple_enabled', 'option')
            && class_exists(\ZipArchive::class)
            && function_exists('openssl_pkcs12_read')
            && function_exists('openssl_pkcs7_sign')
            && $this->getPassTypeIdentifier() !== ''
            && $this->getTeamIdentifier() !== ''
            && is_readable($this->getCertificatePath())
            && is_readable($this->getWwdrCertificatePath());
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
