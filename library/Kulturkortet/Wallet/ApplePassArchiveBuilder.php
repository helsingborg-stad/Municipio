<?php

declare(strict_types=1);

namespace Municipio\Kulturkortet\Wallet;

use RuntimeException;
use ZipArchive;

/** Packages Apple pass payloads, manifest, icon, and signature into a .pkpass archive. */
class ApplePassArchiveBuilder
{
    /**
     * @param AppleWalletConfig $config Apple signing settings.
     * @param ApplePassSigner|null $signer Detached manifest signer.
     */
    public function __construct(
        private AppleWalletConfig $config,
        private ?ApplePassSigner $signer = null,
    ) {}

    /**
     * Builds a signed pass archive.
     *
     * @param array $pass Pass JSON data.
     * @param string $iconContents PNG icon bytes.
     * @param string $workingDirectory Private temporary directory.
     * @return string The complete .pkpass archive.
     * @throws RuntimeException When archive creation fails.
     */
    public function build(array $pass, string $iconContents, string $workingDirectory): string
    {
        $files = [
            'pass.json' => json_encode($pass, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
            'icon.png' => $iconContents,
            'icon@2x.png' => $iconContents,
        ];
        foreach ($files as $name => $contents) {
            $this->writeFile($workingDirectory . '/' . $name, $contents);
        }

        $manifest = [];
        foreach (array_keys($files) as $name) {
            $manifest[$name] = sha1_file($workingDirectory . '/' . $name);
        }
        $manifestContents = json_encode($manifest, JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
        $manifestPath = $workingDirectory . '/manifest.json';
        $this->writeFile($manifestPath, $manifestContents);

        $signer = $this->signer ?? new ApplePassSigner($this->config);
        $this->writeFile($workingDirectory . '/signature', $signer->sign($manifestPath, $workingDirectory));

        return $this->createArchive($workingDirectory);
    }

    /**
     * Adds generated pass files to a ZIP archive and returns its bytes.
     *
     * @param string $workingDirectory Private temporary directory.
     * @return string The .pkpass archive bytes.
     * @throws RuntimeException When a file cannot be archived.
     */
    private function createArchive(string $workingDirectory): string
    {
        $archivePath = $workingDirectory . '/pass.pkpass';
        $archive = new ZipArchive();

        if ($archive->open($archivePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new RuntimeException('Unable to create the Apple Wallet pass archive.');
        }

        foreach (['pass.json', 'manifest.json', 'signature', 'icon.png', 'icon@2x.png'] as $file) {
            if ($archive->addFile($workingDirectory . '/' . $file, $file)) {
                continue;
            }

            $archive->close();
            throw new RuntimeException('Unable to add a file to the Apple Wallet pass archive.');
        }
        $archive->close();

        $contents = file_get_contents($archivePath);
        if (!is_string($contents)) {
            throw new RuntimeException('Unable to read the generated Apple Wallet pass archive.');
        }

        return $contents;
    }

    /**
     * Writes a generated file or throws when the write fails.
     *
     * @param string $path File path.
     * @param string $contents File contents.
     * @return void
     * @throws RuntimeException When the file cannot be written.
     */
    private function writeFile(string $path, string $contents): void
    {
        if (file_put_contents($path, $contents, LOCK_EX) === false) {
            throw new RuntimeException('Unable to write an Apple Wallet pass file.');
        }
    }
}
