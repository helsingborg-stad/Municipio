<?php

declare(strict_types=1);

namespace Municipio\Kulturkortet\Wallet;

use AcfService\Contracts\GetField;
use Municipio\AppleWallet\ApplePassCardConfig;
use Municipio\AppleWallet\AppleWalletConfig;
use Municipio\GoogleWallet\GoogleWalletCardConfig;
use Municipio\GoogleWallet\GoogleWalletConfig;

/**
 * Reads Kulturkortet Wallet settings.
 *
 * Settings shared by the site's wallet accounts come from constants in wp-config.php, and settings specific
 * to a card come from the Kulturkortet Wallet options page.
 */
class WalletSettings
{
    /**
     * @param GetField $acfService ACF field reader.
     */
    public function __construct(private GetField $acfService) {}

    /** @return AppleWalletConfig Apple Wallet settings shared by all pass cards. */
    public function getAppleWalletConfig(): AppleWalletConfig
    {
        return new AppleWalletConfig(
            teamIdentifier: $this->getConstant('MUNICIPIO_WALLET_APPLE_TEAM_IDENTIFIER'),
            certificateDirectory: $this->getConstant('MUNICIPIO_WALLET_APPLE_CERTIFICATE_DIRECTORY'),
            wwdrCertificatePath: $this->getConstant('MUNICIPIO_WALLET_APPLE_WWDR_CERTIFICATE'),
        );
    }

    /** @return ApplePassCardConfig Apple Wallet settings of the Kulturkortet pass card. */
    public function getApplePassCardConfig(): ApplePassCardConfig
    {
        return new ApplePassCardConfig(
            appleWalletConfig: $this->getAppleWalletConfig(),
            enabled: (bool) $this->acfService->getField('kulturkortet_wallet_apple_enabled', 'option'),
            passTypeIdentifier: $this->getString('kulturkortet_wallet_apple_pass_type_identifier'),
            certificateFile: $this->getString('kulturkortet_wallet_apple_certificate'),
            certificatePassword: $this->getRawString('kulturkortet_wallet_apple_certificate_password'),
        );
    }

    /** @return GoogleWalletConfig Google Wallet settings shared by all cards. */
    public function getGoogleWalletConfig(): GoogleWalletConfig
    {
        return new GoogleWalletConfig(
            issuerId: $this->getConstant('MUNICIPIO_WALLET_GOOGLE_ISSUER_ID'),
            serviceAccountEmail: $this->getConstant('MUNICIPIO_WALLET_GOOGLE_SERVICE_ACCOUNT_EMAIL'),
            serviceAccountPrivateKeyPath: $this->getConstant('MUNICIPIO_WALLET_GOOGLE_SERVICE_ACCOUNT_PRIVATE_KEY_PATH'),
        );
    }

    /** @return GoogleWalletCardConfig Google Wallet settings of the Kulturkortet card. */
    public function getGoogleWalletCardConfig(): GoogleWalletCardConfig
    {
        return new GoogleWalletCardConfig(
            googleWalletConfig: $this->getGoogleWalletConfig(),
            enabled: (bool) $this->acfService->getField('kulturkortet_wallet_google_enabled', 'option'),
            classSuffix: $this->getString('kulturkortet_wallet_google_class_suffix'),
        );
    }

    /**
     * Reads a wp-config.php constant.
     *
     * @param string $name Constant name.
     * @return string The trimmed constant value, or an empty string when it is not defined.
     */
    private function getConstant(string $name): string
    {
        $value = defined($name) ? constant($name) : null;
        return is_string($value) ? trim($value) : '';
    }

    /**
     * Reads a string option.
     *
     * @param string $fieldName ACF field name.
     * @return string The trimmed option value, or an empty string.
     */
    private function getString(string $fieldName): string
    {
        return trim($this->getRawString($fieldName));
    }

    /**
     * Reads a string option without trimming it.
     *
     * @param string $fieldName ACF field name.
     * @return string The option value, or an empty string.
     */
    private function getRawString(string $fieldName): string
    {
        $value = $this->acfService->getField($fieldName, 'option');
        return is_string($value) ? $value : '';
    }
}
