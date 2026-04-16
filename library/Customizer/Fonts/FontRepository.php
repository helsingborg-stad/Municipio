<?php

declare(strict_types=1);

namespace Municipio\Customizer\Fonts;

use WpService\WpService;

/**
 * Retrieves uploaded fonts from theme settings and legacy attachments.
 */
class FontRepository
{
    /**
     * @var array<int, string>
     */
    private array $allowedMimes = [
        'application/font-woff',
        'application/font-woff2',
        'font/woff',
        'font/woff2',
    ];

    /**
     * @param WpService $wpService
     * @param UploadedFontMapper|null $uploadedFontMapper
     */
    public function __construct(
        private readonly WpService $wpService,
        private ?UploadedFontMapper $uploadedFontMapper = null,
    ) {
        $this->uploadedFontMapper ??= new UploadedFontMapper($wpService);
    }

    /**
     * Adds supported font mime types.
     *
     * @param array<string, string> $mimes
     *
     * @return array<string, string>
     */
    public function addFontMimes(array $mimes): array
    {
        $mimes['woff']  = 'application/font-woff';
        $mimes['woff2'] = 'application/font-woff2';

        return $mimes;
    }

    /**
     * Returns uploaded fonts from both managed settings and legacy uploads.
     *
     * @return array<string, array{id: int, name: string, type: string, url: string}>
     */
    public function getUploadedFonts(): array
    {
        return array_replace(
            $this->getLegacyUploadedFonts(),
            $this->getManagedUploadedFonts(),
        );
    }

    /**
     * Returns managed uploaded fonts from theme settings.
     *
     * @return array<string, array{id: int, name: string, type: string, url: string}>
     */
    public function getManagedUploadedFonts(): array
    {
        $uploadedFonts = $this->wpService->getThemeMod(FontCatalog::UPLOADED_FONTS_SETTING, []);

        if (!is_array($uploadedFonts)) {
            return [];
        }

        $fonts = [];

        foreach ($uploadedFonts as $uploadedFont) {
            if (
                !is_array($uploadedFont)
                || !array_key_exists('file', $uploadedFont)
                || $uploadedFont['file'] === ''
            ) {
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

    /**
     * Returns legacy uploaded fonts discovered in the media library.
     *
     * @return array<string, array{id: int, name: string, type: string, url: string}>
     */
    public function getLegacyUploadedFonts(): array
    {
        $fontAttachments = new \WP_Query([
            'post_type'      => 'attachment',
            'posts_per_page' => 50,
            'post_status'    => ['publish', 'inherit'],
            'post_mime_type' => $this->allowedMimes,
        ]);

        $fonts = [];

        foreach ($fontAttachments->posts as $fontAttachment) {
            $font = $this->uploadedFontMapper->fromAttachment(
                (int) $fontAttachment->ID,
                $fontAttachment->post_title ?? $this->wpService->__('Untitled Font', 'municipio'),
            );

            if ($font !== null) {
                $fonts[$font['name']] = $font;
            }
        }

        return $fonts;
    }
}
