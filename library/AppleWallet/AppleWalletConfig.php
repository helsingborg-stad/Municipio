<?php

declare(strict_types=1);

namespace Municipio\AppleWallet;

/** Apple Wallet settings shared by every pass card of a site, such as the team and the WWDR certificate. */
readonly class AppleWalletConfig
{
    /**
     * @param string $teamIdentifier Apple Team ID.
     * @param string $certificateDirectory Directory containing the signing certificates of the pass cards.
     * @param string $wwdrCertificatePath Local path to the Apple WWDR certificate in PEM format.
     */
    public function __construct(
        private string $teamIdentifier,
        private string $certificateDirectory,
        private string $wwdrCertificatePath,
    ) {}

    /** @return string The Apple Team ID. */
    public function getTeamIdentifier(): string
    {
        return $this->teamIdentifier;
    }

    /** @return string The directory containing the signing certificates. */
    public function getCertificateDirectory(): string
    {
        return $this->certificateDirectory;
    }

    /** @return string The local path to the Apple WWDR certificate. */
    public function getWwdrCertificatePath(): string
    {
        return $this->wwdrCertificatePath;
    }

    /** @return bool Whether the shared settings and the required PHP extensions are available. */
    public function hasConfiguration(): bool
    {
        return class_exists(\ZipArchive::class)
            && function_exists('openssl_pkcs12_read')
            && function_exists('openssl_pkcs7_sign')
            && $this->teamIdentifier !== ''
            && is_dir($this->certificateDirectory)
            && is_readable($this->wwdrCertificatePath);
    }
}
