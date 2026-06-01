<?php

declare(strict_types=1);

namespace Municipio\Upgrade\V42;

use Kirki\Module\Webfonts\Fonts as KirkiFonts;
use Municipio\Customizer\Fonts\FontCatalog;
use Municipio\Customizer\Fonts\FontRepository;
use Municipio\Customizer\Fonts\FontSettings;
use Municipio\Customizer\Fonts\NativeFontLibraryRepository;
use WpService\WpService;

/**
 * Migrates legacy Municipio font settings to the native WordPress font library.
 */
class MigrateFontsToNativeFontLibrary
{
    public const MIGRATION_SETTING = 'municipio_native_font_library_migrated';

    /**
     * @param WpService $wpService
     * @param FontRepository $fontRepository
     * @param NativeFontLibraryRepository $nativeFontLibraryRepository
     */
    public function __construct(
        private readonly WpService $wpService,
        private readonly FontRepository $fontRepository,
        private readonly NativeFontLibraryRepository $nativeFontLibraryRepository,
    ) {}

    /**
     * Migrates enabled Google font families and uploaded font files.
     *
     * @return void
     */
    public function migrate(): void
    {
        if ((bool) $this->wpService->getThemeMod(self::MIGRATION_SETTING, false)) {
            return;
        }

        if (!$this->nativeFontLibraryRepository->isAvailable()) {
            return;
        }

        foreach ($this->getGoogleFontFamilies() as $fontFamily) {
            $this->nativeFontLibraryRepository->createFontFamilyIfMissing($fontFamily);
        }

        foreach ($this->fontRepository->getUploadedFonts() as $uploadedFont) {
            if (!isset($uploadedFont['name'], $uploadedFont['url']) || $uploadedFont['name'] === '' || $uploadedFont['url'] === '') {
                continue;
            }

            $fontFamilyPostId = $this->nativeFontLibraryRepository->createFontFamilyIfMissing((string) $uploadedFont['name']);

            if ($fontFamilyPostId === null) {
                continue;
            }

            $this->nativeFontLibraryRepository->createFontFaceIfMissing(
                $fontFamilyPostId,
                (string) $uploadedFont['name'],
                (string) $uploadedFont['url'],
            );
        }

        $this->wpService->setThemeMod(self::MIGRATION_SETTING, true);
        $this->wpService->setThemeMod(FontCatalog::MIGRATION_SETTING, true);
    }

    /**
     * Returns Google font families from managed choices and typography selections.
     *
     * @return array<int, string>
     */
    private function getGoogleFontFamilies(): array
    {
        $fontFamilies = $this->wpService->getThemeMod(FontCatalog::GOOGLE_FONTS_SETTING, []);
        $fontFamilies = is_array($fontFamilies) ? $fontFamilies : [];
        $fontFamilies = array_merge(
            $fontFamilies,
            array_keys(array_filter(
                FontSettings::getSelectedFontVariantsFromThemeMods($this->wpService),
                static fn(array $variants, string $fontFamily): bool => KirkiFonts::is_google_font($fontFamily),
                ARRAY_FILTER_USE_BOTH,
            )),
        );

        return array_values(array_unique(array_filter(array_map('strval', $fontFamilies))));
    }
}
