<?php

declare(strict_types=1);

namespace Municipio\Customizer\Fonts;

use WpService\WpService;

/**
 * Prints local @font-face declarations for uploaded fonts.
 */
class UploadedFontFacePrinter
{
    /**
     * @param WpService $wpService
     * @param FontRepository $fontRepository
     */
    public function __construct(
        private readonly WpService $wpService,
        private readonly FontRepository $fontRepository,
    ) {}

    /**
     * Prints uploaded font declarations in the site header.
     *
     * @return void
     */
    public function printDeclarations(): void
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
}
