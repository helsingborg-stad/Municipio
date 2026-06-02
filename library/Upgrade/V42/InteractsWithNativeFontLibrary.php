<?php

declare(strict_types=1);

namespace Municipio\Upgrade\V42;

use WpService\WpService;

/**
 * Provides minimal native font-library persistence helpers for v42 migrations.
 */
trait InteractsWithNativeFontLibrary
{
    abstract protected function getWpService(): WpService;

    private function nativeFontLibraryIsAvailable(): bool
    {
        return $this->getWpService()->postTypeExists('wp_font_family') && $this->getWpService()->postTypeExists('wp_font_face');
    }

    private function createNativeFontFamilyIfMissing(string $fontFamily, ?string $cssFontFamily = null, ?string $preview = null): ?int
    {
        if (!$this->nativeFontLibraryIsAvailable()) {
            return null;
        }

        $fontFamily = trim($fontFamily);

        if ($fontFamily === '') {
            return null;
        }

        $slug = $this->getWpService()->sanitizeTitle($fontFamily);

        if ($slug === '') {
            return null;
        }

        $existingPost = $this->getWpService()->getPageByPath($slug, 'OBJECT', 'wp_font_family');

        if (is_object($existingPost) && property_exists($existingPost, 'ID')) {
            return (int) $existingPost->ID;
        }

        $settings = [
            'name' => $fontFamily,
            'slug' => $slug,
            'fontFamily' => is_string($cssFontFamily) && trim($cssFontFamily) !== '' ? trim($cssFontFamily) : $this->getNativeFontFamilyCssValue($fontFamily),
        ];

        if (is_string($preview) && trim($preview) !== '') {
            $settings['preview'] = trim($preview);
        }

        $postId = $this->getWpService()->wpInsertPost([
            'post_type' => 'wp_font_family',
            'post_status' => 'publish',
            'post_title' => $fontFamily,
            'post_name' => $slug,
            'post_content' => $this->prepareNativeFontPostContent($settings),
        ], true);

        if ($this->isWpError($postId)) {
            return null;
        }

        return is_int($postId) && $postId > 0 ? $postId : null;
    }

    private function createNativeFontFaceIfMissing(
        int $fontFamilyPostId,
        string $fontFamily,
        string|array $source,
        string $fontStyle = 'normal',
        string $fontWeight = '100 900',
        ?string $fontFile = null,
        ?string $unicodeRange = null,
        ?string $preview = null,
    ): void {
        if (!$this->nativeFontLibraryIsAvailable() || $fontFamilyPostId <= 0) {
            return;
        }

        $normalizedSources = $this->normalizeNativeFontSources($source);

        if ($normalizedSources === [] || $this->nativeFontFaceExists($fontFamilyPostId, $normalizedSources)) {
            return;
        }

        $fontFaceSettings = [
            'fontFamily' => $this->getNativeFontFamilyCssValue($fontFamily),
            'fontStyle' => $fontStyle,
            'fontWeight' => $fontWeight,
            'fontDisplay' => 'swap',
            'src' => $normalizedSources,
        ];

        if (is_string($unicodeRange) && trim($unicodeRange) !== '') {
            $fontFaceSettings['unicodeRange'] = trim($unicodeRange);
        }

        if (is_string($preview) && trim($preview) !== '') {
            $fontFaceSettings['preview'] = trim($preview);
        }

        $postId = $this->getWpService()->wpInsertPost([
            'post_type' => 'wp_font_face',
            'post_parent' => $fontFamilyPostId,
            'post_status' => 'publish',
            'post_title' => $this->getNativeFontFaceSlug($fontFaceSettings, $fontFamily, $fontStyle, $fontWeight),
            'post_name' => $this->getNativeFontFaceSlug($fontFaceSettings, $fontFamily, $fontStyle, $fontWeight),
            'post_content' => $this->prepareNativeFontPostContent($fontFaceSettings),
        ], true);

        if ($fontFile !== null && $fontFile !== '' && is_int($postId) && $postId > 0 && !$this->isWpError($postId)) {
            $this->getWpService()->addPostMeta($postId, '_wp_font_face_file', $fontFile);
        }
    }

    /**
     * @param string|array<int, string> $source
     */
    private function nativeFontFaceExists(int $fontFamilyPostId, string|array $source): bool
    {
        $sourcesToFind = $this->normalizeNativeFontSources($source);

        if ($sourcesToFind === []) {
            return false;
        }

        foreach ($this->getWpService()->getPosts([
            'post_type' => 'wp_font_face',
            'post_status' => 'publish',
            'post_parent' => $fontFamilyPostId,
            'posts_per_page' => -1,
            'update_post_meta_cache' => false,
            'update_post_term_cache' => false,
        ]) as $fontFace) {
            $settings = is_object($fontFace) && property_exists($fontFace, 'post_content') ? json_decode((string) $fontFace->post_content, true) : null;
            $sources = is_array($settings) ? $this->normalizeNativeFontSources($settings['src'] ?? []) : [];

            if (array_intersect($sourcesToFind, $sources) !== []) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string|array<int, string> $sources
     *
     * @return array<int, string>
     */
    private function normalizeNativeFontSources(string|array $sources): array
    {
        $sources = is_array($sources) ? $sources : [$sources];

        return array_values(array_unique(array_filter(array_map(
            static fn(mixed $source): string => is_string($source) ? trim($source) : '',
            $sources,
        ))));
    }

    private function getNativeFontFamilyCssValue(string $fontFamily): string
    {
        return sprintf('"%s", sans-serif', trim($fontFamily));
    }

    /**
     * @param array<string, mixed> $settings
     */
    private function prepareNativeFontPostContent(array $settings): string
    {
        $json = $this->getWpService()->wpJsonEncode($settings);

        if (is_string($json) && $json !== '') {
            $slashedJson = $this->getWpService()->wpSlash($json);

            if (is_string($slashedJson)) {
                return $slashedJson;
            }
        }

        return addslashes(is_string($json) ? $json : (string) json_encode($settings));
    }

    /**
     * @param array<string, mixed> $fontFaceSettings
     */
    private function getNativeFontFaceSlug(array $fontFaceSettings, string $fontFamily, string $fontStyle, string $fontWeight): string
    {
        if (class_exists(\WP_Font_Utils::class) && method_exists(\WP_Font_Utils::class, 'get_font_face_slug')) {
            $fontFaceSlug = \WP_Font_Utils::get_font_face_slug($fontFaceSettings);

            if (is_string($fontFaceSlug) && $fontFaceSlug !== '') {
                return $fontFaceSlug;
            }
        }

        $sourceSignature = substr(
            md5(
                is_string($this->getWpService()->wpJsonEncode([
                    'src' => $fontFaceSettings['src'] ?? [],
                    'unicodeRange' => $fontFaceSettings['unicodeRange'] ?? '',
                ]))
                    ? (string) $this->getWpService()->wpJsonEncode([
                        'src' => $fontFaceSettings['src'] ?? [],
                        'unicodeRange' => $fontFaceSettings['unicodeRange'] ?? '',
                    ])
                    : (string) json_encode([
                        'src' => $fontFaceSettings['src'] ?? [],
                        'unicodeRange' => $fontFaceSettings['unicodeRange'] ?? '',
                    ]),
            ),
            0,
            8,
        );

        return $this->getWpService()->sanitizeTitle(sprintf('%s-%s-%s-%s', $fontFamily, $fontStyle, $fontWeight, $sourceSignature));
    }

    private function isWpError(mixed $value): bool
    {
        return $this->getWpService()->isWpError($value);
    }
}
