<?php

declare(strict_types=1);

namespace Municipio\Customizer\Fonts;

use WpService\WpService;

/**
 * Reads managed uploaded fonts from theme settings.
 */
class ManagedUploadedFontRepository
{
    private const LEGACY_UPLOADED_FONTS_SETTING = 'municipio_font_catalog_uploaded_fonts';

    /**
     * @param WpService $wpService
     * @param UploadedFontMapper $uploadedFontMapper
     */
    public function __construct(
        private readonly WpService $wpService,
        private readonly UploadedFontMapper $uploadedFontMapper,
    ) {}

    /**
     * Returns managed uploaded fonts from theme settings.
     *
     * @return array<string, array{id: int, name: string, type: string, url: string}>
     */
    public function getFonts(): array
    {
        $uploadedFonts = $this->wpService->getThemeMod(self::LEGACY_UPLOADED_FONTS_SETTING, []);

        if (!is_array($uploadedFonts)) {
            return [];
        }

        $fonts = [];

        foreach ($uploadedFonts as $uploadedFont) {
            if (!is_array($uploadedFont) || !array_key_exists('file', $uploadedFont) || $uploadedFont['file'] === '') {
                continue;
            }

            $font = $this->uploadedFontMapper->fromUploadValue(
                $uploadedFont['file'],
                null,
            );

            if ($font !== null) {
                $fonts[$this->createFontKey($font)] = $font;
            }
        }

        return $fonts;
    }

    /**
     * Creates a stable key for an uploaded font file.
     *
     * @param array{id: int, name: string, type: string, url: string} $font
     *
     * @return string
     */
    private function createFontKey(array $font): string
    {
        return sprintf('%s|%s', $font['name'], $font['url']);
    }
}
