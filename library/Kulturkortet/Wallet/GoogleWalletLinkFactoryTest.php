<?php

declare(strict_types=1);

namespace Municipio\Kulturkortet\Wallet;

use PHPUnit\Framework\TestCase;

/** Tests Google Wallet save-link creation. */
class GoogleWalletLinkFactoryTest extends TestCase
{
    /** Verifies that ticket data is included in the signed save link. */
    public function testCreatesGoogleWalletSaveLinkWithTicketBarcode(): void
    {
        $link = $this->createGoogleWalletLinkFactory()->createLink([
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
        $factory = $this->createGoogleWalletLinkFactory();

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

    /** Verifies that a disabled integration does not create a link. */
    public function testDoesNotCreateLinkWhenGoogleWalletIsDisabled(): void
    {
        $config = $this->createMock(GoogleWalletConfig::class);
        $config->method('hasConfiguration')->willReturn(false);

        static::assertNull((new GoogleWalletLinkFactory($config))->createLink(['barcode' => 'code']));
    }

    /** Verifies that a ticket without a barcode cannot be added to Google Wallet. */
    public function testDoesNotCreateLinkWithoutBarcode(): void
    {
        $config = $this->createMock(GoogleWalletConfig::class);
        $config->method('hasConfiguration')->willReturn(true);

        static::assertNull((new GoogleWalletLinkFactory($config))->createLink([]));
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

    /** Creates a Google Wallet factory with a temporary RSA signing key. */
    private function createGoogleWalletLinkFactory(): GoogleWalletLinkFactory
    {
        $key = openssl_pkey_new(['private_key_type' => OPENSSL_KEYTYPE_RSA, 'private_key_bits' => 2048]);
        static::assertNotFalse($key);
        static::assertTrue(openssl_pkey_export($key, $privateKey));

        $config = $this->createMock(GoogleWalletConfig::class);
        $config->method('hasConfiguration')->willReturn(true);
        $config->method('getIssuerId')->willReturn('123456789');
        $config->method('getClassSuffix')->willReturn('kulturkortet');
        $config->method('getServiceAccountEmail')->willReturn('wallet@example.com');
        $config->method('getServiceAccountPrivateKey')->willReturn($privateKey);

        return new GoogleWalletLinkFactory($config);
    }
}
