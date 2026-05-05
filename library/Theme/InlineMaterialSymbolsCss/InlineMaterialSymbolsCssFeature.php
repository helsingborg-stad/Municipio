<?php

declare(strict_types=1);

namespace Municipio\Theme\InlineMaterialSymbolsCss;

use Municipio\HooksRegistrar\Hookable;
use WpService\WpService;
use WpUtilService\Features\Enqueue\EnqueueManagerInterface;
use WpUtilService\WpUtilService;

/**
 * Inlines the Material Symbols stylesheet when a built asset is available.
 */
class InlineMaterialSymbolsCssFeature implements Hookable
{
    private const THEME_DIST_DIRECTORY = 'assets/dist/';

    private EnqueueManagerInterface $enqueue;

    /**
     * @param WpService $wpService WordPress service wrapper.
     * @param WpUtilService $wpUtilService Utility service used to resolve the enqueue manager.
     */
    public function __construct(
        private WpService $wpService,
        private WpUtilService $wpUtilService,
    ) {
        $this->enqueue = $this->wpUtilService->enqueue(__DIR__);
    }

    /**
     * Registers the hooks required for the feature.
     */
    public function addHooks(): void
    {
        $this->wpService->addAction('wp_enqueue_scripts', [$this, 'enqueueMaterialSymbols']);
        $this->wpService->addAction('admin_enqueue_scripts', [$this, 'enqueueMaterialSymbols'], 999);
    }

    /**
     * Enqueue Material Symbols font CSS.
     */
    public function enqueueMaterialSymbols(): void
    {
        $weight = $this->wpService->getThemeMod('icon_weight') ?: '400';
        $style  = $this->wpService->getThemeMod('icon_style') ?: 'rounded';

        $weightTranslationTable = [
            '200' => 'light',
            '400' => 'medium',
            '600' => 'bold',
        ];
        $translatedWeight = $weightTranslationTable[$weight] ?? 'medium';

        $src = "fonts/material/{$translatedWeight}/{$style}.css";

        if ($this->enqueueMaterialSymbolsInline($src, $translatedWeight, $style)) {
            return;
        }

        $this->enqueue->add($src);
    }

    /**
     * Inline the Material Symbols stylesheet to support CSP setups that need the CSS content in-page.
     *
     * @param string $src The manifest asset key.
     * @param string $translatedWeight The translated asset weight name.
     * @param string $style The icon style variant.
     *
     * @return bool True when the stylesheet was inlined successfully.
     */
    private function enqueueMaterialSymbolsInline(string $src, string $translatedWeight, string $style): bool
    {
        $assetPath = $this->resolveBuiltAssetPath($src);

        if ($assetPath === null || !is_readable($assetPath)) {
            return false;
        }

        $cssContent = file_get_contents($assetPath);

        if ($cssContent === false || $cssContent === '') {
            return false;
        }

        $handle       = sprintf('material-symbols-%s-%s', $translatedWeight, $style);
        $assetBaseUrl = $this->resolveBuiltAssetBaseUrl($src);

        $this->wpService->wpRegisterStyle($handle, false);
        $this->wpService->wpEnqueueStyle($handle);
        $this->wpService->wpAddInlineStyle($handle, $this->rewriteRelativeAssetUrls($cssContent, $assetBaseUrl));

        return true;
    }

    /**
     * Resolve a built asset path from the theme dist manifest.
     *
     * @param string $src The manifest asset key.
     *
     * @return string|null The absolute asset path when found.
     */
    private function resolveBuiltAssetPath(string $src): ?string
    {
        $assetFile = $this->resolveBuiltAssetFile($src);

        if ($assetFile === null) {
            return null;
        }

        return $this->wpService->getThemeFilePath(self::THEME_DIST_DIRECTORY . ltrim($assetFile, '/'));
    }

    /**
     * Resolve the public base URL for a built asset directory.
     *
     * @param string $src The manifest asset key.
     *
     * @return string The absolute base URL ending with a slash.
     */
    private function resolveBuiltAssetBaseUrl(string $src): string
    {
        $assetFile = $this->resolveBuiltAssetFile($src) ?? $src;

        return rtrim($this->wpService->getStylesheetDirectoryUri(), '/')
            . '/'
            . trim(self::THEME_DIST_DIRECTORY, '/')
            . '/'
            . trim(dirname($assetFile), './')
            . '/';
    }

    /**
     * Resolve the built asset file name from the manifest.
     *
     * @param string $src The manifest asset key.
     *
     * @return string|null The built asset file relative to the dist directory.
     */
    private function resolveBuiltAssetFile(string $src): ?string
    {
        $manifestPath = $this->wpService->getThemeFilePath(self::THEME_DIST_DIRECTORY . 'manifest.json');

        if (!is_readable($manifestPath)) {
            return null;
        }

        $manifest = json_decode((string) file_get_contents($manifestPath), true);

        if (!is_array($manifest)) {
            return null;
        }

        return is_string($manifest[$src] ?? null) ? $manifest[$src] : null;
    }

    /**
     * Rewrite relative asset URLs in CSS so inline styles still resolve sibling font files correctly.
     *
     * @param string $cssContent The CSS content to rewrite.
     * @param string $assetBaseUrl The absolute base URL for the CSS asset directory.
     *
     * @return string The rewritten CSS.
     */
    private function rewriteRelativeAssetUrls(string $cssContent, string $assetBaseUrl): string
    {
        return (string) preg_replace_callback(
            '/url\(([^)]+)\)/',
            static function (array $matches) use ($assetBaseUrl): string {
                $rawUrl = trim($matches[1], " \t\n\r\0\x0B\"'");

                if (
                    $rawUrl === ''
                    || str_starts_with($rawUrl, 'data:')
                    || str_starts_with($rawUrl, '#')
                    || str_starts_with($rawUrl, '/')
                    || str_starts_with($rawUrl, '//')
                    || preg_match('/^[a-z][a-z0-9+.-]*:/i', $rawUrl) === 1
                ) {
                    return 'url(' . $matches[1] . ')';
                }

                return 'url("' . rtrim($assetBaseUrl, '/') . '/' . ltrim($rawUrl, './') . '")';
            },
            $cssContent,
        );
    }
}