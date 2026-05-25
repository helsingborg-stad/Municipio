<?php

declare(strict_types=1);

namespace Municipio\Customizer\Fonts;

use WpService\WpService;

/**
 * Reads managed uploaded fonts from theme settings.
 */
class ManagedUploadedFontRepository
{
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
        $uploadedFonts = $this->wpService->getThemeMod(FontCatalog::UPLOADED_FONTS_SETTING, []);

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
                $fonts[$font['name']] = $font;
            }
        }

        return $fonts;
    }
}
