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
     * @param string $source
     *
     * @return void
     */
    public function createFontFaceIfMissing(int $fontFamilyPostId, string $fontFamily, string $source): void
    {
        if (!$this->shouldCreateFontFace($fontFamilyPostId, $source)) {
            return;
        }

        if (!function_exists('wp_insert_post') || !function_exists('wp_json_encode')) {
            return;
        }

        $postSlug = $this->support->sanitizeSlug($fontFamily . '-normal-100-900');

        wp_insert_post([
            'post_type'    => 'wp_font_face',
            'post_parent'  => $fontFamilyPostId,
            'post_status'  => 'publish',
            'post_title'   => $postSlug,
            'post_name'    => $postSlug,
            'post_content' => wp_json_encode($this->getFontFaceSettings($fontFamily, $source)),
        ], true);
    }

    /**
     * Determines if a font face should be created.
     *
     * @param int $fontFamilyPostId
     * @param string $source
     *
     * @return bool
     */
    private function shouldCreateFontFace(int $fontFamilyPostId, string $source): bool
    {
        return $this->support->isAvailable()
            && $fontFamilyPostId > 0
            && trim($source) !== ''
            && !$this->support->fontFaceExists($fontFamilyPostId, $source);
    }

    /**
     * Returns native font face settings.
     *
     * @param string $fontFamily
     * @param string $source
     *
     * @return array<string, mixed>
     */
    private function getFontFaceSettings(string $fontFamily, string $source): array
    {
        return [
            'fontFamily'  => $this->support->getFontFamilyCssValue($fontFamily),
            'fontStyle'   => 'normal',
            'fontWeight'  => '100 900',
            'fontDisplay' => 'swap',
            'src'         => [$source],
        ];
    }
}
