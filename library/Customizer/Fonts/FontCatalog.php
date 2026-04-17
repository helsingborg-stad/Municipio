<?php

declare(strict_types=1);

namespace Municipio\Customizer\Fonts;

use Municipio\HooksRegistrar\Hookable;
use WpService\WpService;

/**
 * Coordinates font catalog hooks.
 */
class FontCatalog implements Hookable
{
    public const GOOGLE_FONTS_SETTING = 'municipio_font_catalog_google_fonts';
    public const UPLOADED_FONTS_SETTING = 'municipio_font_catalog_uploaded_fonts';
    public const MIGRATION_SETTING = 'municipio_font_catalog_migrated';

    /**
     * @param WpService $wpService
     * @param FontRepository $fontRepository
     * @param GoogleFontsCssLocaleFilter $googleFontsCssLocaleFilter
     * @param FontStyleguideOptionProvider $fontStyleguideOptionProvider
     * @param FontCatalogMigrator $fontCatalogMigrator
     * @param UploadedFontFacePrinter $uploadedFontFacePrinter
     */
    public function __construct(
        private readonly WpService $wpService,
        private readonly FontRepository $fontRepository,
        private readonly GoogleFontsCssLocaleFilter $googleFontsCssLocaleFilter,
        private readonly FontStyleguideOptionProvider $fontStyleguideOptionProvider,
        private readonly FontCatalogMigrator $fontCatalogMigrator,
        private readonly UploadedFontFacePrinter $uploadedFontFacePrinter,
    ) {}

    /**
     * @inheritDoc
     */
    public function addHooks(): void
    {
        $this->wpService->addFilter('upload_mimes', [$this->fontRepository, 'addFontMimes'], 1, 1);
        $this->wpService->addFilter('Municipio/Styleguide/Customize/TokenData/FontFamilies', [$this, 'addStyleguideFontFamilies'], 10, 1);
        $this->googleFontsCssLocaleFilter->addHooks();
        $this->wpService->addAction('init', [$this, 'migrateLegacyFonts']);
        $this->wpService->addAction('wp_head', [$this, 'printFontDeclarations'], 1);
    }

    /**
     * Adds managed font catalog families to styleguide token options.
     *
     * @param array<int, array{value: string, label: string}> $options
     *
     * @return array<int, array{value: string, label: string}>
     */
    public function addStyleguideFontFamilies(array $options): array
    {
        return $this->fontStyleguideOptionProvider->addFontFamilies($options);
    }

    /**
     * Migrates legacy font selections and media-library uploads.
     *
     * @return void
     */
    public function migrateLegacyFonts(): void
    {
        $this->fontCatalogMigrator->migrate();
    }

    /**
     * Prints font declarations in the site header.
     *
     * @return void
     */
    public function printFontDeclarations(): void
    {
        $this->uploadedFontFacePrinter->printDeclarations();
    }
}
