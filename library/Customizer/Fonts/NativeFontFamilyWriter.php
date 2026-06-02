<?php

declare(strict_types=1);

namespace Municipio\Customizer\Fonts;

/**
 * Writes native font family posts.
 */
class NativeFontFamilyWriter
{
    /**
     * @param NativeFontLibrarySupport $support
     */
    public function __construct(
        private readonly NativeFontLibrarySupport $support,
    ) {}

    /**
     * Creates a native font family when it does not already exist.
     *
     * @param string $fontFamily
     * @param string|null $cssFontFamily
     *
     * @return int|null
     */
    public function createFontFamilyIfMissing(string $fontFamily, ?string $cssFontFamily = null, ?string $preview = null): ?int
    {
        if (!$this->support->isAvailable()) {
            return null;
        }

        $fontFamily = trim($fontFamily);

        if ($fontFamily === '') {
            return null;
        }

        $slug = $this->support->sanitizeSlug($fontFamily);
        $existingPost = $this->support->getFontFamilyBySlug($slug);

        if (is_object($existingPost) && property_exists($existingPost, 'ID')) {
            return (int) $existingPost->ID;
        }

        return $this->insertFontFamily($fontFamily, $slug, $cssFontFamily, $preview);
    }

    /**
     * Inserts a native font family post.
     *
     * @param string $fontFamily
     * @param string $slug
     * @param string|null $cssFontFamily
     *
     * @return int|null
     */
    private function insertFontFamily(string $fontFamily, string $slug, ?string $cssFontFamily = null, ?string $preview = null): ?int
    {
        if (!function_exists('wp_insert_post') || !function_exists('wp_json_encode')) {
            return null;
        }

        $settings = [
            'name' => $fontFamily,
            'slug' => $slug,
            'fontFamily' => is_string($cssFontFamily) && trim($cssFontFamily) !== '' ? trim($cssFontFamily) : $this->support->getFontFamilyCssValue($fontFamily),
        ];

        if (is_string($preview) && trim($preview) !== '') {
            $settings['preview'] = trim($preview);
        }

        $postId = wp_insert_post([
            'post_type' => 'wp_font_family',
            'post_status' => 'publish',
            'post_title' => $fontFamily,
            'post_name' => $slug,
            'post_content' => $this->preparePostContent($settings),
        ], true);

        if (function_exists('is_wp_error') && is_wp_error($postId)) {
            return null;
        }

        return is_numeric($postId) && (int) $postId > 0 ? (int) $postId : null;
    }

    /**
     * Prepares JSON post content for wp_insert_post().
     *
     * WordPress unslashes post fields before persistence, so JSON that contains
     * quoted font-family values must be slashed ahead of time to remain valid.
     *
     * @param array<string, mixed> $settings
     *
     * @return string
     */
    private function preparePostContent(array $settings): string
    {
        $json = (string) wp_json_encode($settings);

        if (function_exists('wp_slash')) {
            $slashedJson = wp_slash($json);

            if (is_string($slashedJson)) {
                return $slashedJson;
            }
        }

        return addslashes($json);
    }
}
