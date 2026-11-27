<?php

declare(strict_types=1);

namespace Municipio\AppleWallet;

use SensitiveParameter;

/** Apple Wallet settings of one pass card: its Pass Type ID and signing certificate. */
readonly class ApplePassCardConfig
{
    /**
     * @param AppleWalletConfig $appleWalletConfig Settings shared by all pass cards.
     * @param bool $enabled Whether this pass card is enabled.
     * @param string $passTypeIdentifier Apple Pass Type ID of the card.
     * @param string $certificateFile File name of the card's .p12 signing certificate, inside the shared certificate directory.
     * @param string $certificatePassword Password for the signing certificate, or an empty string when it has none.
     */
    public function __construct(
        private AppleWalletConfig $appleWalletConfig,
        private bool $enabled,
        private string $passTypeIdentifier,
        private string $certificateFile,
        #[SensitiveParameter] private string $certificatePassword,
    ) {}

    /** @return string The Apple Pass Type ID of the card. */
    public function getPassTypeIdentifier(): string
    {
        return $this->passTypeIdentifier;
    }

    /** @return string The Apple Team ID, from the shared settings. */
    public function getTeamIdentifier(): string
    {
        return $this->appleWalletConfig->getTeamIdentifier();
    }

    /** @return string The local path to the card's signing certificate. */
    public function getCertificatePath(): string
    {
        return rtrim($this->appleWalletConfig->getCertificateDirectory(), '/') . '/' . basename($this->certificateFile);
    }

    /** @return string The password for the card's signing certificate. */
    public function getCertificatePassword(): string
    {
        return $this->certificatePassword;
    }

    /** @return string The local path to the Apple WWDR certificate, from the shared settings. */
    public function getWwdrCertificatePath(): string
    {
        return $this->appleWalletConfig->getWwdrCertificatePath();
    }

    /** @return bool Whether the card is enabled and all required settings are available. */
    public function hasConfiguration(): bool
    {
        return $this->enabled
            && $this->appleWalletConfig->hasConfiguration()
            && $this->passTypeIdentifier !== ''
            && basename($this->certificateFile) !== ''
            && is_readable($this->getCertificatePath());
    }
}
