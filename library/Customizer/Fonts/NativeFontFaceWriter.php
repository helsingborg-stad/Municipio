<?php

declare(strict_types=1);

namespace Municipio\Customizer\Fonts;

/**
 * Writes native font face posts.
 */
class NativeFontFaceWriter
{
    /**
     * @param NativeFontLibrarySupport $support
     */
    public function __construct(
        private readonly NativeFontLibrarySupport $support,
    ) {}

    /**
     * Creates a native font face when the source has not already been registered.
     *
     * @param int $fontFamilyPostId
     * @param string $fontFamily
     * @param string|array<int, string> $source
     * @param string $fontStyle
     * @param string $fontWeight
     * @param string|null $fontFile
     * @param string|null $unicodeRange
     *
     * @return void
     */
    public function createFontFaceIfMissing(
        int $fontFamilyPostId,
        string $fontFamily,
        string|array $source,
        string $fontStyle = 'normal',
        string $fontWeight = '100 900',
        ?string $fontFile = null,
        ?string $unicodeRange = null,
        ?string $preview = null,
    ): void {
        $fontFaceSettings = $this->getFontFaceSettings($fontFamily, $source, $fontStyle, $fontWeight, $unicodeRange, $preview);

        if (!$this->shouldCreateFontFace($fontFamilyPostId, $source)) {
            return;
        }

        if (!function_exists('wp_insert_post') || !function_exists('wp_json_encode')) {
            return;
        }

        $postSlug = $this->getFontFaceSlug($fontFaceSettings, $fontFamily, $fontStyle, $fontWeight);

        $postId = wp_insert_post([
            'post_type' => 'wp_font_face',
            'post_parent' => $fontFamilyPostId,
            'post_status' => 'publish',
            'post_title' => $postSlug,
            'post_name' => $postSlug,
            'post_content' => wp_json_encode($fontFaceSettings),
        ], true);

        if ($fontFile !== null && $fontFile !== '' && function_exists('add_post_meta') && (!function_exists('is_wp_error') || !is_wp_error($postId)) && is_numeric($postId)) {
            add_post_meta((int) $postId, '_wp_font_face_file', $fontFile);
        }
    }

    /**
     * Determines if a font face should be created.
     *
     * @param int $fontFamilyPostId
     * @param string|array<int, string> $source
     *
     * @return bool
     */
    private function shouldCreateFontFace(int $fontFamilyPostId, string|array $source): bool
    {
        return $this->support->isAvailable() && $fontFamilyPostId > 0 && $this->normalizeSources($source) !== [] && !$this->support->fontFaceExists($fontFamilyPostId, $source);
    }

    /**
     * Returns native font face settings.
     *
     * @param string $fontFamily
     * @param string|array<int, string> $source
     * @param string $fontStyle
     * @param string $fontWeight
     * @param string|null $unicodeRange
     *
     * @return array<string, mixed>
     */
    private function getFontFaceSettings(string $fontFamily, string|array $source, string $fontStyle, string $fontWeight, ?string $unicodeRange = null, ?string $preview = null): array
    {
        $settings = [
            'fontFamily' => $this->support->getFontFamilyCssValue($fontFamily),
            'fontStyle' => $fontStyle,
            'fontWeight' => $fontWeight,
            'fontDisplay' => 'swap',
            'src' => $this->normalizeSources($source),
        ];

        if (is_string($unicodeRange) && trim($unicodeRange) !== '') {
            $settings['unicodeRange'] = trim($unicodeRange);
        }

        if (is_string($preview) && trim($preview) !== '') {
            $settings['preview'] = trim($preview);
        }

        return $settings;
    }

    /**
     * @param array<string, mixed> $fontFaceSettings
     * @param string $fontFamily
     * @param string $fontStyle
     * @param string $fontWeight
     *
     * @return string
     */
    private function getFontFaceSlug(array $fontFaceSettings, string $fontFamily, string $fontStyle, string $fontWeight): string
    {
        if (class_exists(\WP_Font_Utils::class) && method_exists(\WP_Font_Utils::class, 'get_font_face_slug')) {
            $fontFaceSlug = \WP_Font_Utils::get_font_face_slug($fontFaceSettings);

            if (is_string($fontFaceSlug) && $fontFaceSlug !== '') {
                return $fontFaceSlug;
            }
        }

        $sourceSignature = substr(
            md5((string) wp_json_encode([
                'src' => $fontFaceSettings['src'] ?? [],
                'unicodeRange' => $fontFaceSettings['unicodeRange'] ?? '',
            ])),
            0,
            8,
        );

        return $this->support->sanitizeSlug(sprintf('%s-%s-%s-%s', $fontFamily, $fontStyle, $fontWeight, $sourceSignature));
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
}
