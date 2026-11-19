<?php

declare(strict_types=1);

namespace Municipio\Kulturkortet\Wallet;

use RuntimeException;

/** Builds signed .pkpass archives for Kulturkortet tickets. */
class ApplePassGenerator
{
    /** @var string Category whose template is used when none is specified. */
    private const DEFAULT_CATEGORY = 'ordinarie';

    /**
     * @param AppleWalletConfig $config Apple Wallet signing settings.
     * @param ApplePassArchiveBuilder|null $archiveBuilder Pass archive builder.
     * @param ApplePassDataFactory|null $passDataFactory Creates pass payloads.
     * @param ApplePassTemplateLoader|null $templateLoader Loads category pass templates.
     */
    public function __construct(
        private AppleWalletConfig $config,
        private ?ApplePassArchiveBuilder $archiveBuilder = null,
        private ?ApplePassDataFactory $passDataFactory = null,
        private ?ApplePassTemplateLoader $templateLoader = null,
    ) {}

    /**
     * Generates a signed Apple Wallet pass.
     *
     * @param array $ticket Active Vitec ticket data.
     * @return string The complete .pkpass archive.
     * @throws RuntimeException When configuration or pass generation fails.
     */
    public function generate(array $ticket): string
    {
        if (!$this->config->hasConfiguration()) {
            throw new RuntimeException('Apple Wallet is not fully configured.');
        }

        $barcode = $ticket['barcode'] ?? null;
        if (!is_string($barcode) || $barcode === '') {
            throw new RuntimeException('The ticket does not contain a barcode.');
        }

        $workingDirectory = sys_get_temp_dir() . '/kulturkortet-pass-' . bin2hex(random_bytes(16));
        if (!mkdir($workingDirectory, 0o700)) {
            throw new RuntimeException('Unable to create a temporary Apple Wallet pass directory.');
        }

        try {
            $templateLoader = $this->templateLoader ?? new ApplePassTemplateLoader();
            $images = $templateLoader->loadImages(self::DEFAULT_CATEGORY);

            $archiveBuilder = $this->archiveBuilder ?? new ApplePassArchiveBuilder($this->config);
            $passDataFactory = $this->passDataFactory ?? new ApplePassDataFactory($this->config, $templateLoader);
            $passData = $passDataFactory->create($ticket, $barcode, self::DEFAULT_CATEGORY);
            return $archiveBuilder->build($passData, $images, $workingDirectory);
        } finally {
            $generatedFiles = glob($workingDirectory . '/*');
            foreach (is_array($generatedFiles) ? $generatedFiles : [] as $path) {
                if (is_file($path)) {
                    unlink($path);
                }
            }
            rmdir($workingDirectory);
        }
    }
}
