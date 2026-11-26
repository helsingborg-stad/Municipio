<?php

declare(strict_types=1);

namespace Municipio\AppleWallet;

use RuntimeException;

/** Builds signed Apple Wallet .pkpass archives from a category template and caller-provided placeholders. */
class ApplePassGenerator
{
    /**
     * @param ApplePassCardConfig $config Apple Wallet signing settings.
     * @param ApplePassTemplateLoader $templateLoader Loads category pass templates.
     * @param ApplePassArchiveBuilder|null $archiveBuilder Pass archive builder.
     * @param ApplePassDataFactory|null $passDataFactory Creates pass payloads.
     */
    public function __construct(
        private ApplePassCardConfig $config,
        private ApplePassTemplateLoader $templateLoader,
        private ?ApplePassArchiveBuilder $archiveBuilder = null,
        private ?ApplePassDataFactory $passDataFactory = null,
    ) {}

    /**
     * Generates a signed Apple Wallet pass.
     *
     * @param string $category Category slug selecting the pass template.
     * @param array<string, string> $placeholders Placeholder replacements keyed by placeholder.
     * @return string The complete .pkpass archive.
     * @throws RuntimeException When configuration or pass generation fails.
     */
    public function generate(string $category, array $placeholders): string
    {
        if (!$this->config->hasConfiguration()) {
            throw new RuntimeException('Apple Wallet is not fully configured.');
        }

        $workingDirectory = sys_get_temp_dir() . '/apple-wallet-pass-' . bin2hex(random_bytes(16));
        if (!mkdir($workingDirectory, 0o700)) {
            throw new RuntimeException('Unable to create a temporary Apple Wallet pass directory.');
        }

        try {
            $images = $this->templateLoader->loadImages($category);

            $archiveBuilder = $this->archiveBuilder ?? new ApplePassArchiveBuilder($this->config);
            $passDataFactory = $this->passDataFactory ?? new ApplePassDataFactory($this->config, $this->templateLoader);
            $passData = $passDataFactory->create($category, $placeholders);
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
