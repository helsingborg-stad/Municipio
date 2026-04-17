<?php

declare(strict_types=1);

namespace Municipio\Customizer\Fonts;

use Kirki\Module\Webfonts\Fonts as KirkiFonts;
use WpService\WpService;

/**
 * Migrates legacy font settings into the managed font catalog.
 */
class FontCatalogMigrator
{
    /**
     * @param WpService $wpService
     * @param ManagedFonts $managedFonts
     * @param LegacyUploadedFontRepository $legacyUploadedFontRepository
     */
    public function __construct(
        private readonly WpService $wpService,
        private readonly ManagedFonts $managedFonts,
        private readonly LegacyUploadedFontRepository $legacyUploadedFontRepository,
    ) {}

    /**
     * Migrates legacy font selections and media-library uploads.
     *
     * @return void
     */
    public function migrate(): void
    {
        if ((bool) $this->wpService->getThemeMod(FontCatalog::MIGRATION_SETTING, false)) {
            return;
        }

        $this->wpService->setThemeMod(FontCatalog::GOOGLE_FONTS_SETTING, $this->getMigratedGoogleFonts());
        $this->wpService->setThemeMod(FontCatalog::UPLOADED_FONTS_SETTING, $this->getMigratedUploadedFonts());
        $this->wpService->setThemeMod(FontCatalog::MIGRATION_SETTING, true);
    }

    /**
     * Returns selected Google fonts from the current theme settings.
     *
     * @return array<int, string>
     */
    private function getSelectedGoogleFonts(): array
    {
        $fontFamilies = FontChoices::getEnabledGoogleFonts();

        return array_values(array_filter($fontFamilies, KirkiFonts::is_google_font(...)));
    }

    /**
     * Returns migrated Google fonts.
     *
     * @return array<int, string>
     */
    private function getMigratedGoogleFonts(): array
    {
        $enabledGoogleFonts = $this->wpService->getThemeMod(FontCatalog::GOOGLE_FONTS_SETTING, []);
        $enabledGoogleFonts = is_array($enabledGoogleFonts) ? $enabledGoogleFonts : [];
        $enabledGoogleFonts = array_merge($enabledGoogleFonts, $this->getSelectedGoogleFonts());
        $enabledGoogleFonts = array_values(array_unique(array_filter(array_map('strval', $enabledGoogleFonts))));

        return $enabledGoogleFonts !== [] ? $enabledGoogleFonts : ['Roboto'];
    }

    /**
     * Returns migrated uploaded fonts.
     *
     * @return array<int, array{file: int|string}>
     */
    private function getMigratedUploadedFonts(): array
    {
        $uploadedFonts = $this->wpService->getThemeMod(FontCatalog::UPLOADED_FONTS_SETTING, []);
        $uploadedFonts = is_array($uploadedFonts) ? $uploadedFonts : [];

        return $this->managedFonts->mergeUploadedFontRows(
            $uploadedFonts,
            $this->mapLegacyUploadedFontsToRows(),
        );
    }

    /**
     * Maps legacy uploaded fonts into managed rows.
     *
     * @return array<int, array{file: string}>
     */
    private function mapLegacyUploadedFontsToRows(): array
    {
        return array_values(array_map(
            static fn(array $font): array => [
                'file' => $font['url'],
            ],
            $this->legacyUploadedFontRepository->getFonts(),
        ));
    }
}
