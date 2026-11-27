<?php

declare(strict_types=1);

namespace Municipio\AppleWallet;

use RuntimeException;

/** Creates the detached DER-encoded PKCS#7 signature required by Apple Wallet. */
class ApplePassSigner
{
    /**
     * @param ApplePassCardConfig $config Apple signing settings.
     */
    public function __construct(private ApplePassCardConfig $config) {}

    /**
     * Signs a manifest and extracts its detached DER signature.
     *
     * @param string $manifestPath Path to the manifest.
     * @param string $workingDirectory Private temporary directory.
     * @return string DER-encoded signature bytes.
     * @throws RuntimeException When the certificate cannot be loaded or signing fails.
     */
    public function sign(string $manifestPath, string $workingDirectory): string
    {
        $p12 = file_get_contents($this->config->getCertificatePath());
        if (!is_string($p12) || !openssl_pkcs12_read($p12, $certificates, $this->config->getCertificatePassword())) {
            throw new RuntimeException('Unable to read the Apple Wallet signing certificate.');
        }

        $certificate = openssl_x509_read($certificates['cert'] ?? '');
        $privateKey = openssl_pkey_get_private($certificates['pkey'] ?? '');
        if ($certificate === false || $privateKey === false) {
            throw new RuntimeException('Unable to load the Apple Wallet signing certificate and key.');
        }

        $signedPath = $workingDirectory . '/signed-manifest';
        if (!openssl_pkcs7_sign(
            $manifestPath,
            $signedPath,
            $certificate,
            $privateKey,
            [],
            PKCS7_DETACHED | PKCS7_BINARY,
            $this->config->getWwdrCertificatePath(),
        )) {
            throw new RuntimeException('Unable to sign the Apple Wallet pass manifest.');
        }

        return $this->extractSignature($signedPath);
    }

    /**
     * Reads the S/MIME result and returns the signature as DER bytes.
     *
     * @param string $signedPath Path to the generated S/MIME message.
     * @return string DER-encoded signature bytes.
     * @throws RuntimeException When the signature cannot be extracted.
     */
    private function extractSignature(string $signedPath): string
    {
        $smime = file_get_contents($signedPath);
        if (!is_string($smime) || preg_match('/Content-Type:\\s*application\\/x-pkcs7-signature[\\s\\S]*?\\r?\\n\\r?\\n([A-Za-z0-9+\\/=\\r\\n]+)/i', $smime, $matches) !== 1) {
            throw new RuntimeException('Unable to extract the Apple Wallet pass signature.');
        }

        $signature = base64_decode(preg_replace('/\\s+/', '', $matches[1]), true);
        if (!is_string($signature)) {
            throw new RuntimeException('The Apple Wallet pass signature is not valid base64.');
        }

        return $signature;
    }
}
