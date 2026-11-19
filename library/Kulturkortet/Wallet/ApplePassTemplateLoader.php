<?php

declare(strict_types=1);

namespace Municipio\Kulturkortet\Wallet;

use RuntimeException;

/** Loads Apple Wallet pass templates (pass.json and images) for a Kulturkortet category. */
class ApplePassTemplateLoader
{
    /** @var string Directory containing one sub-directory per category. */
    private string $templateDirectory;

    /**
     * @param string|null $templateDirectory Directory containing category templates. Defaults to the bundled templates.
     */
    public function __construct(?string $templateDirectory = null)
    {
        $this->templateDirectory = $templateDirectory ?? __DIR__ . '/PassTemplates';
    }

    /**
     * Reads and decodes the pass.json definition for a category.
     *
     * @param string $category Category slug, e.g. "ordinarie".
     * @return array The decoded pass definition, including placeholders.
     * @throws RuntimeException When the category or its pass.json is missing or invalid.
     */
    public function loadPassDefinition(string $category): array
    {
        $contents = $this->readFile($this->getCategoryDirectory($category) . '/pass.json');
        $definition = json_decode($contents, true);
        if (!is_array($definition)) {
            throw new RuntimeException('The Apple Wallet pass template is not valid JSON.');
        }

        return $definition;
    }

    /**
     * Reads every PNG image in a category's template directory.
     *
     * @param string $category Category slug, e.g. "ordinarie".
     * @return array<string, string> Image file contents keyed by file name.
     * @throws RuntimeException When the category directory cannot be read.
     */
    public function loadImages(string $category): array
    {
        $paths = glob($this->getCategoryDirectory($category) . '/*.png');
        if (!is_array($paths) || $paths === []) {
            throw new RuntimeException('The Apple Wallet pass template has no images.');
        }

        $images = [];
        foreach ($paths as $path) {
            $images[basename($path)] = $this->readFile($path);
        }

        return $images;
    }

    /**
     * Resolves the directory for a category after validating its slug.
     *
     * @param string $category Category slug.
     * @return string Absolute path to the category directory.
     * @throws RuntimeException When the slug is invalid or the directory does not exist.
     */
    private function getCategoryDirectory(string $category): string
    {
        if (preg_match('/^[a-z0-9-]+$/', $category) !== 1) {
            throw new RuntimeException('Invalid Apple Wallet pass category.');
        }

        $directory = $this->templateDirectory . '/' . $category;
        if (!is_dir($directory)) {
            throw new RuntimeException('No Apple Wallet pass template exists for this category.');
        }

        return $directory;
    }

    /**
     * Reads a template file or throws when it cannot be read.
     *
     * @param string $path Absolute file path.
     * @return string File contents.
     * @throws RuntimeException When the file cannot be read.
     */
    private function readFile(string $path): string
    {
        $contents = is_file($path) ? file_get_contents($path) : false;
        if (!is_string($contents) || $contents === '') {
            throw new RuntimeException('Unable to read an Apple Wallet pass template file.');
        }

        return $contents;
    }
}
