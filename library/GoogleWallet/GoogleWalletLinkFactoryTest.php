<?php

declare(strict_types=1);

namespace Municipio\GoogleWallet;

use PHPUnit\Framework\TestCase;

/** Tests Google Wallet save-link creation for generic objects. */
class GoogleWalletLinkFactoryTest extends TestCase
{
    private ?string $keyPath = null;

    /** Removes the temporary private key file. */
    protected function tearDown(): void
    {
        if ($this->keyPath !== null) {
            unlink($this->keyPath);
        }
        parent::tearDown();
    }

    /** Verifies that the object identifiers and caller-provided fields are included in the signed save link. */
    public function testCreatesSaveLinkWithObjectIdentifiersAndFields(): void
    {
        $object = $this->getGenericObject($this->createGoogleWalletLinkFactory(true)->createLink('object-1', [
            'barcode' => ['type' => 'QR_CODE', 'value' => 'code'],
        ]));

        static::assertSame('123456789.object-1', $object['id']);
        static::assertSame('123456789.example', $object['classId']);
        static::assertSame('ACTIVE', $object['state']);
        static::assertSame('code', $object['barcode']['value']);
    }

    /** Verifies that a disabled card does not create a link. */
    public function testDoesNotCreateLinkWhenCardIsDisabled(): void
    {
        static::assertNull($this->createGoogleWalletLinkFactory(false)->createLink('object-1', []));
    }

    /**
     * Decodes the signed generic object from a Google Wallet save link.
     *
     * @param string|null $link Google Wallet save link.
     * @return array<string, mixed> Decoded generic object.
     */
    private function getGenericObject(?string $link): array
    {
        static::assertIsString($link);
        $token = substr($link, strlen('https://pay.google.com/gp/v/save/'));
        $segments = explode('.', $token);
        $decodedPayload = base64_decode(strtr($segments[1], '-_', '+/'), true);
        static::assertIsString($decodedPayload);
        $claims = json_decode($decodedPayload, true, flags: JSON_THROW_ON_ERROR);

        return $claims['payload']['genericObjects'][0];
    }

    /**
     * Creates a Google Wallet factory for a card with a temporary RSA signing key.
     *
     * @param bool $enabled Whether the card is enabled.
     * @return GoogleWalletLinkFactory A factory for the "example" class.
     */
    private function createGoogleWalletLinkFactory(bool $enabled): GoogleWalletLinkFactory
    {
        $key = openssl_pkey_new(['private_key_type' => OPENSSL_KEYTYPE_RSA, 'private_key_bits' => 2048]);
        static::assertNotFalse($key);
        static::assertTrue(openssl_pkey_export($key, $privateKey));

        $this->keyPath = tempnam(sys_get_temp_dir(), 'google-wallet-key-');
        file_put_contents($this->keyPath, $privateKey);

        $shared = new GoogleWalletConfig('123456789', 'wallet@example.com', $this->keyPath);

        return new GoogleWalletLinkFactory(new GoogleWalletCardConfig($shared, $enabled, 'example'));
    }
}
