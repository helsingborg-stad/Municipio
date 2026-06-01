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
     *
     * @return int|null
     */
    public function createFontFamilyIfMissing(string $fontFamily): ?int
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

        return $this->insertFontFamily($fontFamily, $slug);
    }

    /**
     * Inserts a native font family post.
     *
     * @param string $fontFamily
     * @param string $slug
     *
     * @return int|null
     */
    private function insertFontFamily(string $fontFamily, string $slug): ?int
    {
        if (!function_exists('wp_insert_post') || !function_exists('wp_json_encode')) {
            return null;
        }

        $postId = wp_insert_post([
            'post_type'    => 'wp_font_family',
            'post_status'  => 'publish',
            'post_title'   => $fontFamily,
            'post_name'    => $slug,
            'post_content' => wp_json_encode([
                'fontFamily' => $this->support->getFontFamilyCssValue($fontFamily),
            ]),
        ], true);

        if (function_exists('is_wp_error') && is_wp_error($postId)) {
            return null;
        }

        return is_numeric($postId) && (int) $postId > 0 ? (int) $postId : null;
    }
}
