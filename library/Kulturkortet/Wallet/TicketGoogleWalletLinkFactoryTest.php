<?php

declare(strict_types=1);

namespace Municipio\Kulturkortet\Wallet;

use Municipio\GoogleWallet\GoogleWalletCardConfig;
use Municipio\GoogleWallet\GoogleWalletConfig;
use Municipio\GoogleWallet\GoogleWalletLinkFactory;
use PHPUnit\Framework\TestCase;

/** Tests Google Wallet save-link creation for Kulturkortet tickets. */
class TicketGoogleWalletLinkFactoryTest extends TestCase
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

    /** Verifies that ticket data is included in the signed save link. */
    public function testCreatesGoogleWalletSaveLinkWithTicketBarcode(): void
    {
        $link = $this->createGoogleWalletLinkFactory(true)->createLink([
            'id' => 'ticket-1',
            'firstname' => 'Ada',
            'lastname' => 'Lovelace',
            'barcode' => 'ticket-barcode',
            'validUntil' => '2030-12-31',
        ]);

        static::assertIsString($link);
        static::assertStringStartsWith('https://pay.google.com/gp/v/save/', $link);
        $token = substr($link, strlen('https://pay.google.com/gp/v/save/'));
        $segments = explode('.', $token);
        static::assertCount(3, $segments);
        $decodedPayload = base64_decode(strtr($segments[1], '-_', '+/'), true);
        static::assertIsString($decodedPayload);
        $claims = json_decode($decodedPayload, true, flags: JSON_THROW_ON_ERROR);

        static::assertSame('wallet@example.com', $claims['iss']);
        static::assertSame('google', $claims['aud']);
        static::assertSame('savetowallet', $claims['typ']);
        static::assertSame('ticket-barcode', $claims['payload']['genericObjects'][0]['barcode']['value']);
        static::assertSame('123456789.kulturkortet', $claims['payload']['genericObjects'][0]['classId']);
    }

    /** Verifies that renewed tickets receive distinct objects with their original expiration time. */
    public function testUsesBarcodeForObjectIdentityAndPreservesExpirationDateTime(): void
    {
        $factory = $this->createGoogleWalletLinkFactory(true);

        $firstTicket = ['id' => 'customer-1', 'barcode' => 'ticket-old', 'validUntil' => '2030-12-31T23:59:58+02:00'];
        $renewedTicket = [...$firstTicket, 'barcode' => 'ticket-renewed'];
        $firstObject = $this->getGenericObject($factory->createLink($firstTicket));
        $renewedObject = $this->getGenericObject($factory->createLink($renewedTicket));

        static::assertNotSame($firstObject['id'], $renewedObject['id']);
        static::assertSame(
            '2030-12-31T23:59:58+02:00',
            $firstObject['validTimeInterval']['end']['dateTime'],
        );
    }

    /** Verifies that a disabled Google Wallet card does not create a link. */
    public function testDoesNotCreateLinkWhenGoogleWalletIsDisabled(): void
    {
        static::assertNull($this->createGoogleWalletLinkFactory(false)->createLink(['barcode' => 'code']));
    }

    /** Verifies that a ticket without a barcode cannot be added to Google Wallet. */
    public function testDoesNotCreateLinkWithoutBarcode(): void
    {
        static::assertNull($this->createGoogleWalletLinkFactory(true)->createLink([]));
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
     * Creates the Kulturkortet Google Wallet factory with a temporary RSA signing key.
     *
     * @param bool $enabled Whether the Kulturkortet card is enabled.
     * @return TicketGoogleWalletLinkFactory A factory for the "kulturkortet" class.
     */
    private function createGoogleWalletLinkFactory(bool $enabled): TicketGoogleWalletLinkFactory
    {
        $key = openssl_pkey_new(['private_key_type' => OPENSSL_KEYTYPE_RSA, 'private_key_bits' => 2048]);
        static::assertNotFalse($key);
        static::assertTrue(openssl_pkey_export($key, $privateKey));

        $this->keyPath = tempnam(sys_get_temp_dir(), 'google-wallet-key-');
        file_put_contents($this->keyPath, $privateKey);

        $shared = new GoogleWalletConfig('123456789', 'wallet@example.com', $this->keyPath);
        $card = new GoogleWalletCardConfig($shared, $enabled, 'kulturkortet');

        return new TicketGoogleWalletLinkFactory(new GoogleWalletLinkFactory($card));
    }
}
