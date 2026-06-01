<?php

declare(strict_types=1);

namespace Municipio\Customizer\Fonts;

/**
 * Provides low-level helpers for WordPress' native font library post types.
 */
class NativeFontLibrarySupport
{
    /**
     * Returns whether the native font library post types are available.
     *
     * @return bool
     */
    public function isAvailable(): bool
    {
        return function_exists('post_type_exists')
            && post_type_exists('wp_font_family')
            && post_type_exists('wp_font_face');
    }

    /**
     * Returns a CSS font-family value for native font settings.
     *
     * @param string $fontFamily
     *
     * @return string
     */
    public function getFontFamilyCssValue(string $fontFamily): string
    {
        return sprintf('"%s", sans-serif', trim($fontFamily));
    }

    /**
     * Finds a native font family by slug.
     *
     * @param string $slug
     *
     * @return object|null
     */
    public function getFontFamilyBySlug(string $slug): ?object
    {
        if (!function_exists('get_page_by_path')) {
            return null;
        }

        $post = get_page_by_path($slug, OBJECT, 'wp_font_family');

        return is_object($post) ? $post : null;
    }

    /**
     * Checks if a font face with the same source exists for a font family.
     *
     * @param int $fontFamilyPostId
     * @param string $source
     *
     * @return bool
     */
    public function fontFaceExists(int $fontFamilyPostId, string $source): bool
    {
        if (!function_exists('get_posts')) {
            return false;
        }

        foreach ($this->getFontFacePosts($fontFamilyPostId) as $fontFace) {
            $settings = is_object($fontFace) && property_exists($fontFace, 'post_content')
                ? json_decode((string) $fontFace->post_content, true)
                : null;

            if (!is_array($settings) || !array_key_exists('src', $settings)) {
                continue;
            }

            $sources = is_array($settings['src']) ? $settings['src'] : [$settings['src']];

            if (in_array($source, $sources, true)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Sanitizes a font library slug.
     *
     * @param string $value
     *
     * @return string
     */
    public function sanitizeSlug(string $value): string
    {
        if (function_exists('sanitize_title')) {
            return sanitize_title($value);
        }

        return strtolower(trim((string) preg_replace('/[^a-z0-9]+/i', '-', $value), '-'));
    }

    /**
     * Returns native font face posts for a font family.
     *
     * @param int $fontFamilyPostId
     *
     * @return array<int, object>
     */
    private function getFontFacePosts(int $fontFamilyPostId): array
    {
        return get_posts([
            'post_type'              => 'wp_font_face',
            'post_status'            => 'publish',
            'post_parent'            => $fontFamilyPostId,
            'posts_per_page'         => -1,
            'update_post_meta_cache' => false,
            'update_post_term_cache' => false,
        ]);
    }
}
