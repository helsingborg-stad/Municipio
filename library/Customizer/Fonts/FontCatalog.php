<?php

declare(strict_types=1);

namespace Municipio\Customizer\Fonts;

use Kirki\Module\Webfonts\Fonts as KirkiFonts;
use Municipio\HooksRegistrar\Hookable;
use WpService\WpService;

/**
 * Manages the site font catalogue and frontend font declarations.
 */
class FontCatalog implements Hookable
{
    public const GOOGLE_FONTS_SETTING = 'municipio_font_catalog_google_fonts';
    public const UPLOADED_FONTS_SETTING = 'municipio_font_catalog_uploaded_fonts';
    public const MIGRATION_SETTING = 'municipio_font_catalog_migrated';

    /**
     * @param WpService $wpService
     * @param ManagedFonts|null $managedFonts
     * @param FontRepository|null $fontRepository
     */
    public function __construct(
        private readonly WpService $wpService,
        private ?ManagedFonts $managedFonts = null,
        private ?FontRepository $fontRepository = null,
        private ?GoogleFontsCssLocaleFilter $googleFontsCssLocaleFilter = null,
    ) {
        $this->managedFonts ??= new ManagedFonts();
        $this->fontRepository ??= new FontRepository($wpService);
        $this->googleFontsCssLocaleFilter ??= new GoogleFontsCssLocaleFilter($wpService);
    }

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
        $googleFonts = $this->wpService->getThemeMod(self::GOOGLE_FONTS_SETTING, []);
        $googleFonts = is_array($googleFonts) ? $googleFonts : [];
        $googleFonts = array_values(array_unique(array_filter(array_map('strval', $googleFonts))));

        foreach ($googleFonts as $fontFamily) {
            $options[] = $this->createFontFamilyOption($fontFamily);
        }

        foreach ($this->fontRepository->getUploadedFonts() as $uploadedFont) {
            if (!isset($uploadedFont['name']) || $uploadedFont['name'] === '') {
                continue;
            }

            $options[] = $this->createFontFamilyOption((string) $uploadedFont['name']);
        }

        return $this->getUniqueOptionsByValue($options);
    }

    /**
     * Migrates legacy font selections and media-library uploads.
     *
     * @return void
     */
    public function migrateLegacyFonts(): void
    {
        if ((bool) $this->wpService->getThemeMod(self::MIGRATION_SETTING, false)) {
            return;
        }

        $this->wpService->setThemeMod(self::GOOGLE_FONTS_SETTING, $this->getMigratedGoogleFonts());
        $this->wpService->setThemeMod(self::UPLOADED_FONTS_SETTING, $this->getMigratedUploadedFonts());
        $this->wpService->setThemeMod(self::MIGRATION_SETTING, true);
    }

    /**
     * Prints font declarations in the site header.
     *
     * @return void
     */
    public function printFontDeclarations(): void
    {
        $uploadedFonts = $this->fontRepository->getUploadedFonts();
        if ($uploadedFonts === []) {
            return;
        }

        echo '<style id="municipio-uploaded-fonts">';

        foreach ($uploadedFonts as $font) {
            $fontFaceRule = sprintf(
                '@font-face{font-display:swap;font-family:"%s";src:url("%s") format("%s");font-weight:100 900;}',
                $this->wpService->escAttr($font['name']),
                $this->wpService->escUrl($font['url']),
                $this->wpService->escAttr($font['type'] !== '' ? $font['type'] : 'woff'),
            );

            echo $this->wpService->wpStripAllTags($fontFaceRule);
        }

        echo '</style>';
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
        $enabledGoogleFonts = $this->wpService->getThemeMod(self::GOOGLE_FONTS_SETTING, []);
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
        $uploadedFonts = $this->wpService->getThemeMod(self::UPLOADED_FONTS_SETTING, []);
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
            $this->fontRepository->getLegacyUploadedFonts(),
        ));
    }

    /**
     * Creates a styleguide-select option from a font family name.
     *
     * @param string $fontFamily
     *
     * @return array{value: string, label: string}
     */
    private function createFontFamilyOption(string $fontFamily): array
    {
        return [
            'value' => sprintf('"%s", sans-serif', trim($fontFamily)),
            'label' => trim($fontFamily),
        ];
    }

    /**
     * Removes duplicated styleguide options by value while preserving order.
     *
     * @param array<int, array{value: string, label: string}> $options
     *
     * @return array<int, array{value: string, label: string}>
     */
    private function getUniqueOptionsByValue(array $options): array
    {
        $uniqueOptions = [];

        foreach ($options as $option) {
            if (!isset($option['value'], $option['label']) || !is_string($option['value']) || !is_string($option['label'])) {
                continue;
            }

            if ($option['value'] === '') {
                continue;
            }

            $uniqueOptions[$option['value']] = $option;
        }

        return array_values($uniqueOptions);
    }
}
