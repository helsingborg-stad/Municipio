<?php

declare(strict_types=1);

namespace Municipio\Customizer\Fonts;

use WpService\WpService;

/**
 * Reads legacy uploaded fonts from media-library attachments.
 */
class LegacyUploadedFontRepository
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
     * @param UploadedFontMapper $uploadedFontMapper
     */
    public function __construct(
        private readonly WpService $wpService,
        private readonly UploadedFontMapper $uploadedFontMapper,
    ) {}

    /**
     * Adds supported font mime types.
     *
     * @param array<string, string> $mimes
     *
     * @return array<string, string>
     */
    public function addFontMimes(array $mimes): array
    {
        $mimes['woff'] = 'application/font-woff';
        $mimes['woff2'] = 'application/font-woff2';

        return $mimes;
    }

    /**
     * Returns legacy uploaded fonts discovered in the media library.
     *
     * @return array<string, array{id: int, name: string, type: string, url: string}>
     */
    public function getFonts(): array
    {
        $fontAttachments = new \WP_Query([
            'post_type' => 'attachment',
            'posts_per_page' => 50,
            'post_status' => ['publish', 'inherit'],
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
