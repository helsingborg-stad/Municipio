<?php

declare(strict_types=1);

namespace Municipio\Upgrade\V42;

use Closure;
use Municipio\Customizer\Fonts\FontCatalog;
use Municipio\Customizer\Fonts\FontSettings;
use Municipio\Customizer\Fonts\NativeFontLibraryRepository;
use WpService\WpService;

/**
 * Detects previously used Kirki Google fonts and installs them through
 * WordPress' native font collection data.
 */
class MigrateKirkiFontsToNativeFontLibrary
{
    public const MIGRATION_SETTING = 'municipio_native_font_library_kirki_fonts_migrated';

    /**
     * @var Closure(): array<string, array{name: string, fontFamily: string, fontFace: array<int, array<string, mixed>>, preview?: string}>
     */
    private readonly Closure $installableFontsProvider;

    public function __construct(
        private readonly WpService $wpService,
        private readonly NativeFontLibraryRepository $nativeFontLibraryRepository,
        ?Closure $installableFontsProvider = null,
    ) {
        $this->installableFontsProvider = $installableFontsProvider ?? fn(): array => $this->getInstallableFontsFromWordPressCollections();
    }

    /**
     * Installs previously used Google fonts into native font-family and font-face posts.
     */
    public function migrate(): void
    {
        if ((bool) $this->wpService->getThemeMod(self::MIGRATION_SETTING, false)) {
            return;
        }

        if (!$this->nativeFontLibraryRepository->isAvailable()) {
            return;
        }

        foreach ($this->getPreviouslyUsedInstallableFonts(($this->installableFontsProvider)()) as $font) {
            $fontFamilyPostId = $this->nativeFontLibraryRepository->createFontFamilyIfMissing(
                $font['name'],
                $font['fontFamily'],
                $font['preview'] ?? null,
            );

            if ($fontFamilyPostId === null) {
                continue;
            }

            foreach ($font['fontFace'] as $fontFace) {
                $sources = $this->normalizeSources($fontFace['src'] ?? []);

                if ($sources === []) {
                    continue;
                }

                $this->nativeFontLibraryRepository->createFontFaceIfMissing(
                    $fontFamilyPostId,
                    $font['name'],
                    $sources,
                    isset($fontFace['fontStyle']) ? (string) $fontFace['fontStyle'] : 'normal',
                    isset($fontFace['fontWeight']) ? (string) $fontFace['fontWeight'] : '400',
                    unicodeRange: isset($fontFace['unicodeRange']) && is_string($fontFace['unicodeRange']) ? $fontFace['unicodeRange'] : null,
                    preview: isset($fontFace['preview']) && is_string($fontFace['preview']) ? $fontFace['preview'] : null,
                );
            }
        }

        $this->wpService->setThemeMod(self::MIGRATION_SETTING, true);
    }

    /**
     * Returns installable WordPress font collection entries for previously used font families.
     *
     * @param array<string, array{name: string, fontFamily: string, fontFace: array<int, array<string, mixed>>, preview?: string}> $installableFonts
     *
     * @return array<string, array{name: string, fontFamily: string, fontFace: array<int, array<string, mixed>>, preview?: string}>
     */
    private function getPreviouslyUsedInstallableFonts(array $installableFonts): array
    {
        $installableFonts = $this->normalizeInstallableFontsIndex($installableFonts);
        $catalogFonts = $this->wpService->getThemeMod(FontCatalog::GOOGLE_FONTS_SETTING, []);
        $catalogFonts = is_array($catalogFonts) ? array_values(array_filter(array_map('strval', $catalogFonts))) : [];

        $selectedFamilies = array_values(array_unique(array_merge(
            FontSettings::getSelectedFontFamiliesFromThemeMods($this->wpService),
            $catalogFonts,
        )));

        $fontsToInstall = [];

        foreach ($selectedFamilies as $fontFamily) {
            $fontKey = $this->normalizeFontFamilyName($fontFamily);

            if (!isset($installableFonts[$fontKey])) {
                continue;
            }

            $fontsToInstall[$fontKey] = $installableFonts[$fontKey];
        }

        ksort($fontsToInstall);

        return $fontsToInstall;
    }

    /**
     * @param array<string, array{name: string, fontFamily: string, fontFace: array<int, array<string, mixed>>, preview?: string}> $installableFonts
     *
     * @return array<string, array{name: string, fontFamily: string, fontFace: array<int, array<string, mixed>>, preview?: string}>
     */
    private function normalizeInstallableFontsIndex(array $installableFonts): array
    {
        $normalizedFonts = [];

        foreach ($installableFonts as $fontKey => $font) {
            if (!is_array($font)) {
                continue;
            }

            $normalizedKey = $this->normalizeFontFamilyName((string) $fontKey);

            if ($normalizedKey === '' && isset($font['name']) && is_string($font['name'])) {
                $normalizedKey = $this->normalizeFontFamilyName($font['name']);
            }

            if ($normalizedKey === '') {
                continue;
            }

            $normalizedFonts[$normalizedKey] = $font;
        }

        return $normalizedFonts;
    }

    /**
     * Returns installable font families from WordPress' registered font collections.
     *
     * @return array<string, array{name: string, fontFamily: string, fontFace: array<int, array<string, mixed>>, preview?: string}>
     */
    private function getInstallableFontsFromWordPressCollections(): array
    {
        if (!class_exists(\WP_Font_Library::class) || !method_exists(\WP_Font_Library::class, 'get_instance')) {
            return [];
        }

        $fontLibrary = \WP_Font_Library::get_instance();

        if (!is_object($fontLibrary) || !method_exists($fontLibrary, 'get_font_collections')) {
            return [];
        }

        $installableFonts = [];

        foreach ((array) $fontLibrary->get_font_collections() as $collection) {
            if (!is_object($collection) || !method_exists($collection, 'get_data')) {
                continue;
            }

            $collectionData = $collection->get_data();

            if (function_exists('is_wp_error') && is_wp_error($collectionData) || !is_array($collectionData)) {
                continue;
            }

            foreach ($collectionData['font_families'] ?? [] as $fontDefinition) {
                if (!is_array($fontDefinition)) {
                    continue;
                }

                $font = $this->normalizeInstallableFont($fontDefinition);

                if ($font === null) {
                    continue;
                }

                foreach ($this->createInstallableFontKeys($fontDefinition, $font) as $fontKey) {
                    $installableFonts[$fontKey] ??= $font;
                }
            }
        }

        return $installableFonts;
    }

    /**
     * @param array<string, mixed> $fontDefinition
     *
     * @return array{name: string, fontFamily: string, fontFace: array<int, array<string, mixed>>, preview?: string}|null
     */
    private function normalizeInstallableFont(array $fontDefinition): ?array
    {
        $fontFamilySettings = $fontDefinition['font_family_settings'] ?? null;

        if (!is_array($fontFamilySettings)) {
            return null;
        }

        $fontFamilyCssValue = isset($fontFamilySettings['fontFamily']) ? trim((string) $fontFamilySettings['fontFamily']) : '';
        $fontFaces = isset($fontFamilySettings['fontFace']) && is_array($fontFamilySettings['fontFace']) ? array_values(array_filter($fontFamilySettings['fontFace'], 'is_array')) : [];
        $fontName = isset($fontFamilySettings['name']) ? trim((string) $fontFamilySettings['name']) : '';

        if ($fontName === '') {
            $fontName = $this->normalizeFontFamilyName($fontFamilyCssValue);
        }

        if ($fontName === '' || $fontFamilyCssValue === '' || $fontFaces === []) {
            return null;
        }

        $font = [
            'name' => $fontName,
            'fontFamily' => $fontFamilyCssValue,
            'fontFace' => $fontFaces,
        ];

        if (isset($fontFamilySettings['preview']) && is_string($fontFamilySettings['preview']) && trim($fontFamilySettings['preview']) !== '') {
            $font['preview'] = trim($fontFamilySettings['preview']);
        }

        return $font;
    }

    /**
     * @param array<string, mixed> $fontDefinition
     * @param array{name: string, fontFamily: string, fontFace: array<int, array<string, mixed>>, preview?: string} $font
     *
     * @return array<int, string>
     */
    private function createInstallableFontKeys(array $fontDefinition, array $font): array
    {
        $fontFamilySettings = $fontDefinition['font_family_settings'] ?? [];
        $fontKeys = [
            $this->normalizeFontFamilyName($font['name']),
            $this->normalizeFontFamilyName($font['fontFamily']),
            isset($fontFamilySettings['slug']) ? trim((string) $fontFamilySettings['slug']) : '',
        ];

        return array_values(array_unique(array_filter($fontKeys)));
    }

    /**
     * @param string|array<int, string> $sources
     *
     * @return array<int, string>
     */
    private function normalizeSources(string|array $sources): array
    {
        $sources = is_array($sources) ? $sources : [$sources];

        return array_values(array_unique(array_filter(array_map(
            static fn(mixed $source): string => is_string($source) ? trim($source) : '',
            $sources,
        ))));
    }

    /**
     * Normalizes a CSS font-family value to the primary family name.
     *
     * @param string $fontFamily
     *
     * @return string
     */
    private function normalizeFontFamilyName(string $fontFamily): string
    {
        $fontFamily = trim(strtok($fontFamily, ','));
        return strtolower(trim($fontFamily, " \t\n\r\0\x0B\"'"));
    }
}
