<?php

declare(strict_types=1);

namespace Municipio\Kulturkortet\Wallet;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use ZipArchive;

/** Tests signed Apple Wallet pass generation. */
class ApplePassGeneratorTest extends TestCase
{
    private string $temporaryDirectory;

    /** Creates isolated files for generated pass artifacts. */
    protected function setUp(): void
    {
        parent::setUp();
        $this->temporaryDirectory = sys_get_temp_dir() . '/kulturkortet-test-' . bin2hex(random_bytes(8));
        mkdir($this->temporaryDirectory, 0o700);
    }

    /** Removes test certificate and archive files. */
    protected function tearDown(): void
    {
        $files = glob($this->temporaryDirectory . '/*');
        foreach (is_array($files) ? $files : [] as $path) {
            if (!is_file($path)) {
                continue;
            }
            unlink($path);
        }
        rmdir($this->temporaryDirectory);
        parent::tearDown();
    }

    /** Verifies that the archive contains ticket data, a manifest, and a DER signature. */
    public function testGeneratesZipPassWithTicketAndSignatureFiles(): void
    {
        $paths = $this->createSigningFiles();
        $config = $this->createMock(AppleWalletConfig::class);
        $config->method('hasConfiguration')->willReturn(true);
        $config->method('getPassTypeIdentifier')->willReturn('pass.example.kulturkortet');
        $config->method('getTeamIdentifier')->willReturn('TEAM12345');
        $config->method('getCertificatePath')->willReturn($paths['certificate']);
        $config->method('getCertificatePassword')->willReturn('passphrase');
        $config->method('getWwdrCertificatePath')->willReturn($paths['wwdr']);
        $config->method('getIconPath')->willReturn($paths['icon']);

        $contents = (new ApplePassGenerator($config))->generate([
            'id' => 'ticket-1',
            'firstname' => 'Ada',
            'lastname' => 'Lovelace',
            'validFrom' => '2030-01-01',
            'validUntil' => '2030-12-31',
            'barcode' => 'ticket-barcode',
        ]);

        $archivePath = $this->temporaryDirectory . '/result.pkpass';
        file_put_contents($archivePath, $contents);
        $archive = new ZipArchive();
        static::assertTrue($archive->open($archivePath));
        $pass = json_decode($archive->getFromName('pass.json'), true, flags: JSON_THROW_ON_ERROR);
        $manifest = json_decode($archive->getFromName('manifest.json'), true, flags: JSON_THROW_ON_ERROR);
        $signature = $archive->getFromName('signature');
        static::assertIsString($signature);

        static::assertSame('pass.example.kulturkortet', $pass['passTypeIdentifier']);
        static::assertSame('ticket-barcode', $pass['barcode']['message']);
        static::assertSame("\x30", $signature[0]);
        static::assertArrayHasKey('signature', array_fill_keys($this->getArchiveNames($archive), true));
        static::assertArrayHasKey('pass.json', $manifest);
        static::assertSame(hash('sha1', $archive->getFromName('pass.json')), $manifest['pass.json']);
        $archive->close();
    }

    /** Verifies that malformed ticket data is rejected before signing. */
    public function testRejectsTicketsWithoutBarcode(): void
    {
        $config = $this->createMock(AppleWalletConfig::class);
        $config->method('hasConfiguration')->willReturn(true);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The ticket does not contain a barcode.');

        (new ApplePassGenerator($config))->generate([]);
    }

    /** @return array<string, string> Temporary signing fixture paths. */
    private function createSigningFiles(): array
    {
        $key = openssl_pkey_new(['private_key_type' => OPENSSL_KEYTYPE_RSA, 'private_key_bits' => 2048]);
        static::assertNotFalse($key);
        $request = openssl_csr_new(['commonName' => 'Wallet Test'], $key);
        static::assertNotFalse($request);
        $certificate = openssl_csr_sign($request, null, $key, 365);
        static::assertNotFalse($certificate);
        static::assertTrue(openssl_pkcs12_export($certificate, $p12, $key, 'passphrase'));
        static::assertTrue(openssl_x509_export($certificate, $pemCertificate));

        $certificatePath = $this->temporaryDirectory . '/certificate.p12';
        $wwdrPath = $this->temporaryDirectory . '/wwdr.pem';
        $iconPath = $this->temporaryDirectory . '/icon.png';
        file_put_contents($certificatePath, $p12);
        file_put_contents($wwdrPath, $pemCertificate);
        file_put_contents($iconPath, "\x89PNG\r\n\x1a\nwallet-icon");

        return [
            'certificate' => $certificatePath,
            'wwdr' => $wwdrPath,
            'icon' => $iconPath,
        ];
    }

    /**
     * @param ZipArchive $archive Open pass archive.
     * @return string[] Archive member names.
     */
    private function getArchiveNames(ZipArchive $archive): array
    {
        $names = [];
        for ($index = 0; $index < $archive->numFiles; $index++) {
            $name = $archive->getNameIndex($index);
            if (is_string($name)) {
                $names[] = $name;
            }
        }

        return $names;
    }
}
