<?php

declare(strict_types=1);

namespace Municipio\Customizer\Fonts;

/**
 * Reads and writes fonts through WordPress' native font library post types.
 */
class NativeFontLibraryRepository
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
     * Returns installed native font family names.
     *
     * @return array<int, string>
     */
    public function getFontFamilies(): array
    {
        if (!$this->isAvailable() || !function_exists('get_posts')) {
            return [];
        }

        $posts = get_posts([
            'post_type'              => 'wp_font_family',
            'post_status'            => 'publish',
            'posts_per_page'         => -1,
            'orderby'                => 'title',
            'order'                  => 'ASC',
            'update_post_meta_cache' => false,
            'update_post_term_cache' => false,
        ]);

        return array_values(array_unique(array_filter(array_map(
            static fn($post): string => is_object($post) && isset($post->post_title) ? (string) $post->post_title : '',
            $posts,
        ))));
    }

    /**
     * Creates a native font family when it does not already exist.
     *
     * @param string $fontFamily
     *
     * @return int|null
     */
    public function createFontFamilyIfMissing(string $fontFamily): ?int
    {
        if (!$this->isAvailable()) {
            return null;
        }

        $fontFamily = trim($fontFamily);

        if ($fontFamily === '') {
            return null;
        }

        $slug = $this->sanitizeSlug($fontFamily);
        $existingPost = $this->getFontFamilyBySlug($slug);

        if (is_object($existingPost) && isset($existingPost->ID)) {
            return (int) $existingPost->ID;
        }

        if (!function_exists('wp_insert_post') || !function_exists('wp_json_encode')) {
            return null;
        }

        $postId = wp_insert_post([
            'post_type'    => 'wp_font_family',
            'post_status'  => 'publish',
            'post_title'   => $fontFamily,
            'post_name'    => $slug,
            'post_content' => wp_json_encode([
                'fontFamily' => $this->getFontFamilyCssValue($fontFamily),
            ]),
        ], true);

        if (function_exists('is_wp_error') && is_wp_error($postId)) {
            return null;
        }

        return is_numeric($postId) && (int) $postId > 0 ? (int) $postId : null;
    }

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
        if (!$this->isAvailable() || $fontFamilyPostId <= 0 || trim($source) === '' || $this->fontFaceExists($fontFamilyPostId, $source)) {
            return;
        }

        if (!function_exists('wp_insert_post') || !function_exists('wp_json_encode')) {
            return;
        }

        $settings = [
            'fontFamily'  => $this->getFontFamilyCssValue($fontFamily),
            'fontStyle'   => 'normal',
            'fontWeight'  => '100 900',
            'fontDisplay' => 'swap',
            'src'         => [$source],
        ];

        wp_insert_post([
            'post_type'    => 'wp_font_face',
            'post_parent'  => $fontFamilyPostId,
            'post_status'  => 'publish',
            'post_title'   => $this->sanitizeSlug($fontFamily . '-normal-100-900'),
            'post_name'    => $this->sanitizeSlug($fontFamily . '-normal-100-900'),
            'post_content' => wp_json_encode($settings),
        ], true);
    }

    /**
     * Returns a CSS font-family value for native font settings.
     *
     * @param string $fontFamily
     *
     * @return string
     */
    private function getFontFamilyCssValue(string $fontFamily): string
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
    private function getFontFamilyBySlug(string $slug): ?object
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
    private function fontFaceExists(int $fontFamilyPostId, string $source): bool
    {
        if (!function_exists('get_posts')) {
            return false;
        }

        $fontFaces = get_posts([
            'post_type'              => 'wp_font_face',
            'post_status'            => 'publish',
            'post_parent'            => $fontFamilyPostId,
            'posts_per_page'         => -1,
            'update_post_meta_cache' => false,
            'update_post_term_cache' => false,
        ]);

        foreach ($fontFaces as $fontFace) {
            $settings = is_object($fontFace) && isset($fontFace->post_content)
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
    private function sanitizeSlug(string $value): string
    {
        if (function_exists('sanitize_title')) {
            return sanitize_title($value);
        }

        return strtolower(trim((string) preg_replace('/[^a-z0-9]+/i', '-', $value), '-'));
    }
}
