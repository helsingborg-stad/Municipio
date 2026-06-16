<?php

declare(strict_types=1);

namespace Municipio\Upgrade\V43;

use Closure;
use Municipio\Upgrade\V42\InteractsWithNativeFontLibrary;
use WpService\WpService;

/**
 * Migrates legacy Kirki uploaded fonts into the native WordPress font library.
 */
class MigrateLegacyUploadedFontsToNativeFontLibrary
{
    use InteractsWithNativeFontLibrary;

    private const LEGACY_UPLOADED_FONTS_SETTING = 'municipio_font_catalog_uploaded_fonts';
    private const ACTIVATION_SETTING = 'municipio_native_font_library_legacy_uploaded_fonts_v43_activated';

    public const MIGRATION_SETTING = 'municipio_native_font_library_legacy_uploaded_fonts_v43_migrated';

    /**
     * @var array<int, string>
     */
    private array $allowedMimes = [
        'application/vnd.ms-opentype',
        'application/font-woff',
        'application/font-woff2',
        'application/x-font-ttf',
        'font/otf',
        'font/ttf',
        'font/woff',
        'font/woff2',
    ];

    /**
     * @var Closure(): array<int, array{id: int, name: string, type: string, url: string}>
     */
    private readonly Closure $uploadedFontsProvider;

    /**
     * @var Closure(array{id: int, name: string, type: string, url: string}): array{source: string, fontFile: string|null}|null
     */
    private readonly Closure $fontSourceResolver;

    /**
     * @var Closure(array{name: string, slug: string, fontFamily: string, fontFace: array<int, array<string, mixed>>}): bool
     */
    private readonly Closure $fontActivationPersister;

    public function __construct(
        private readonly WpService $wpService,
        ?Closure $uploadedFontsProvider = null,
        ?Closure $fontSourceResolver = null,
        ?Closure $fontActivationPersister = null,
    ) {
        $this->uploadedFontsProvider = $uploadedFontsProvider ?? fn(): array => $this->getUploadedFonts();
        $this->fontSourceResolver = $fontSourceResolver ?? fn(array $font): ?array => $this->resolveNativeFontSource($font);
        $this->fontActivationPersister = $fontActivationPersister ?? fn(array $activatedFont): bool => $this->persistActivatedFontFamily($activatedFont);
    }

    protected function getWpService(): WpService
    {
        return $this->wpService;
    }

    /**
     * Migrates legacy uploaded fonts into native font-family and font-face posts.
     */
    public function migrate(): void
    {
        if ((bool) $this->wpService->getThemeMod(self::MIGRATION_SETTING, false)) {
            return;
        }

        if (!$this->nativeFontLibraryIsAvailable()) {
            return;
        }

        $allFontsMigrated = true;
        $activatedFonts = [];

        foreach (($this->uploadedFontsProvider)() as $font) {
            if (($font['name'] ?? '') === '' || ($font['url'] ?? '') === '') {
                $allFontsMigrated = false;
                continue;
            }

            $fontFamilyPostId = $this->createNativeFontFamilyIfMissing((string) $font['name']);

            if ($fontFamilyPostId === null) {
                $allFontsMigrated = false;
                continue;
            }

            $resolvedSource = ($this->fontSourceResolver)($font);

            if (!is_array($resolvedSource) || ($resolvedSource['source'] ?? '') === '') {
                $allFontsMigrated = false;
                continue;
            }

            $this->createNativeFontFaceIfMissing(
                $fontFamilyPostId,
                (string) $font['name'],
                (string) $resolvedSource['source'],
                'normal',
                '100 900',
                isset($resolvedSource['fontFile']) && is_string($resolvedSource['fontFile']) ? $resolvedSource['fontFile'] : null,
            );

            if (!$this->nativeFontFaceExists($fontFamilyPostId, (string) $resolvedSource['source'])) {
                $allFontsMigrated = false;
                continue;
            }

            $activatedFonts[$fontFamilyPostId] = $this->createActivatedFontFamily(
                $fontFamilyPostId,
                (string) $font['name'],
            );
        }

        if (!(bool) $this->wpService->getThemeMod(self::ACTIVATION_SETTING, false)) {
            $allFontsActivated = $activatedFonts !== [];

            foreach (array_values(array_filter($activatedFonts, 'is_array')) as $activatedFont) {
                if (!($this->fontActivationPersister)($activatedFont)) {
                    $allFontsActivated = false;
                }
            }

            if ($allFontsActivated) {
                $this->wpService->setThemeMod(self::ACTIVATION_SETTING, true);
            }
        }

        if ($allFontsMigrated) {
            $this->cleanupLegacyUploadedFonts();
        }

        $this->wpService->setThemeMod(self::MIGRATION_SETTING, true);
    }

    /**
     */
    private function cleanupLegacyUploadedFonts(): void
    {
        $this->wpService->setThemeMod(self::LEGACY_UPLOADED_FONTS_SETTING, []);
    }

    /**
     * Returns uploaded fonts from both the post-bridge managed rows and the pre-bridge attachment scan.
     *
     * @return array<int, array{id: int, name: string, type: string, url: string}>
     */
    private function getUploadedFonts(): array
    {
        return $this->mergeUploadedFonts(
            $this->getManagedFonts(),
            $this->getLegacyAttachmentFonts(),
        );
    }

    /**
     * @param array<int, array{id: int, name: string, type: string, url: string}> ...$fontCollections
     *
     * @return array<int, array{id: int, name: string, type: string, url: string}>
     */
    private function mergeUploadedFonts(array ...$fontCollections): array
    {
        $fonts = [];

        foreach ($fontCollections as $fontCollection) {
            foreach ($fontCollection as $font) {
                $fonts[$this->createFontKey($font)] = $font;
            }
        }

        return array_values($fonts);
    }

    /**
     * @return array<int, array{id: int, name: string, type: string, url: string}>
     */
    private function getManagedFonts(): array
    {
        $uploadedFonts = $this->wpService->getThemeMod(self::LEGACY_UPLOADED_FONTS_SETTING, []);

        if (!is_array($uploadedFonts)) {
            return [];
        }

        $fonts = [];

        foreach ($uploadedFonts as $uploadedFont) {
            if (!is_array($uploadedFont) || !isset($uploadedFont['file']) || $uploadedFont['file'] === '') {
                continue;
            }

            $font = $this->mapUploadValueToFont($uploadedFont['file']);

            if ($font !== null) {
                $fonts[] = $font;
            }
        }

        return $fonts;
    }

    /**
     * @return array<int, array{id: int, name: string, type: string, url: string}>
     */
    private function getLegacyAttachmentFonts(): array
    {
        if (!class_exists(\WP_Query::class)) {
            return [];
        }

        $fontAttachments = new \WP_Query([
            'post_type' => 'attachment',
            'posts_per_page' => -1,
            'post_status' => ['publish', 'inherit'],
            'post_mime_type' => $this->getLegacyAttachmentMimeTypes(),
        ]);

        $fonts = [];

        foreach ($fontAttachments->posts as $fontAttachment) {
            if (!is_object($fontAttachment) || !isset($fontAttachment->ID)) {
                continue;
            }

            $font = $this->mapAttachmentToFont(
                (int) $fontAttachment->ID,
                isset($fontAttachment->post_title) ? (string) $fontAttachment->post_title : null,
            );

            if ($font !== null) {
                $fonts[] = $font;
            }
        }

        return $fonts;
    }

    /**
     * @return array<int, string>
     */
    private function getLegacyAttachmentMimeTypes(): array
    {
        return array_values(array_unique($this->allowedMimes));
    }

    /**
     * @param int|string $file
     *
     * @return array{id: int, name: string, type: string, url: string}|null
     */
    private function mapUploadValueToFont(int|string $file): ?array
    {
        if (is_numeric($file)) {
            return $this->mapAttachmentToFont((int) $file);
        }

        if (!is_string($file) || $file === '') {
            return null;
        }

        $extension = pathinfo(basename($file), PATHINFO_EXTENSION);

        return [
            'id' => 0,
            'name' => $this->deriveFontNameFromFilePath($file),
            'type' => $extension !== '' ? $extension : 'woff',
            'url' => $file,
        ];
    }

    /**
     * @return array{id: int, name: string, type: string, url: string}|null
     */
    private function mapAttachmentToFont(int $attachmentId, ?string $fontName = null): ?array
    {
        if ($attachmentId === 0) {
            return null;
        }

        $url = $this->wpService->wpGetAttachmentUrl($attachmentId);

        if (!is_string($url) || $url === '') {
            return null;
        }

        $fileType = $this->wpService->wpCheckFiletypeAndExt($url, basename($url));

        return [
            'id' => $attachmentId,
            'name' => is_string($fontName) && $fontName !== '' ? $fontName : $this->deriveFontNameFromFilePath($url),
            'type' => $fileType['ext'] ?? pathinfo(basename($url), PATHINFO_EXTENSION),
            'url' => $url,
        ];
    }

    /**
     * @param array{id: int, name: string, type: string, url: string} $font
     *
     * @return array{source: string, fontFile: string|null}|null
     */
    private function resolveNativeFontSource(array $font): ?array
    {
        $sourceFromAttachment = $font['id'] > 0 ? $this->installAttachmentInFontDirectory($font['id']) : null;

        if ($sourceFromAttachment !== null) {
            return $sourceFromAttachment;
        }

        $sourceFromUrl = $this->installUrlInFontDirectory($font['url']);

        if ($sourceFromUrl !== null) {
            return $sourceFromUrl;
        }

        return [
            'source' => $font['url'],
            'fontFile' => null,
        ];
    }

    /**
     * @return array{source: string, fontFile: string|null}|null
     */
    private function installAttachmentInFontDirectory(int $attachmentId): ?array
    {
        $filePath = $this->wpService->getAttachedFile($attachmentId);

        if (!is_string($filePath) || $filePath === '') {
            return null;
        }

        return $this->installFileInFontDirectory($filePath, basename($filePath));
    }

    /**
     * @return array{source: string, fontFile: string|null}|null
     */
    private function installUrlInFontDirectory(string $url): ?array
    {
        if ($url === '') {
            return null;
        }

        $fontDir = $this->wpService->wpGetFontDir();

        if (!is_array($fontDir) || empty($fontDir['baseurl']) || empty($fontDir['basedir'])) {
            return null;
        }

        if (str_starts_with($url, rtrim((string) $fontDir['baseurl'], '/') . '/')) {
            $relativePath = ltrim(substr($url, strlen(rtrim((string) $fontDir['baseurl'], '/'))), '/');

            return [
                'source' => $url,
                'fontFile' => $relativePath !== '' ? $relativePath : null,
            ];
        }

        $uploadDir = $this->wpService->wpUploadDir();

        if (!is_array($uploadDir) || empty($uploadDir['baseurl']) || empty($uploadDir['basedir'])) {
            return null;
        }

        $baseUrl = rtrim((string) $uploadDir['baseurl'], '/');

        if (!str_starts_with($url, $baseUrl . '/')) {
            return null;
        }

        $relativePath = ltrim(substr($url, strlen($baseUrl)), '/');
        $filePath = rtrim((string) $uploadDir['basedir'], DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relativePath);

        return $this->installFileInFontDirectory($filePath, basename($filePath));
    }

    /**
     * @return array{source: string, fontFile: string|null}|null
     */
    private function installFileInFontDirectory(string $sourcePath, ?string $preferredFileName = null): ?array
    {
        if ($sourcePath === '' || !is_readable($sourcePath)) {
            return null;
        }

        $fontDir = $this->wpService->wpGetFontDir();

        if (!is_array($fontDir) || empty($fontDir['basedir']) || empty($fontDir['baseurl'])) {
            return null;
        }

        $fontBaseDir = rtrim((string) $fontDir['basedir'], DIRECTORY_SEPARATOR);
        $fontBaseUrl = rtrim((string) $fontDir['baseurl'], '/');
        $sourceRealPath = realpath($sourcePath);

        if (!is_string($sourceRealPath) || $sourceRealPath === '') {
            return null;
        }

        $fontBaseRealPath = realpath($fontBaseDir) ?: $fontBaseDir;

        if (str_starts_with($sourceRealPath, rtrim($fontBaseRealPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR)) {
            $relativePath = ltrim(substr($sourceRealPath, strlen($fontBaseRealPath)), DIRECTORY_SEPARATOR);

            return [
                'source' => $fontBaseUrl . '/' . str_replace(DIRECTORY_SEPARATOR, '/', $relativePath),
                'fontFile' => str_replace(DIRECTORY_SEPARATOR, '/', $relativePath),
            ];
        }

        $temporaryFile = $this->wpService->wpTempnam($sourcePath);

        if ($temporaryFile === '') {
            return null;
        }

        if (!copy($sourceRealPath, $temporaryFile)) {
            if (file_exists($temporaryFile)) {
                unlink($temporaryFile);
            }

            return null;
        }

        $fileName = is_string($preferredFileName) && $preferredFileName !== '' ? $preferredFileName : basename($sourceRealPath);
        $file = [
            'name' => $fileName,
            'tmp_name' => $temporaryFile,
            'error' => 0,
            'size' => (int) filesize($temporaryFile),
        ];

        $overrides = [
            'test_form' => false,
        ];

        if (class_exists(\WP_Font_Utils::class) && method_exists(\WP_Font_Utils::class, 'get_allowed_font_mime_types')) {
            $overrides['mimes'] = \WP_Font_Utils::get_allowed_font_mime_types();
        }

        $this->wpService->addFilter('upload_dir', '_wp_filter_font_directory');

        $sideloadedFile = $this->wpService->wpHandleSideload($file, $overrides);

        $this->wpService->removeFilter('upload_dir', '_wp_filter_font_directory');

        if (!is_array($sideloadedFile) || empty($sideloadedFile['file']) || empty($sideloadedFile['url'])) {
            if (file_exists($temporaryFile)) {
                unlink($temporaryFile);
            }

            return null;
        }

        return [
            'source' => (string) $sideloadedFile['url'],
            'fontFile' => $this->relativeFontsPath((string) $sideloadedFile['file']),
        ];
    }

    private function relativeFontsPath(string $path): string
    {
        if ($path === '') {
            return $path;
        }

        $fontDir = $this->wpService->wpGetFontDir();
        $baseDir = isset($fontDir['basedir']) && is_string($fontDir['basedir']) ? rtrim($fontDir['basedir'], '/') : '';

        if ($baseDir !== '' && str_starts_with($path, $baseDir)) {
            return ltrim(substr($path, strlen($baseDir)), '/');
        }

        return $path;
    }

    /**
     * @param array{id: int, name: string, type: string, url: string} $font
     */
    private function createFontKey(array $font): string
    {
        if (($font['url'] ?? '') !== '') {
            return 'url:' . (string) $font['url'];
        }

        if (($font['id'] ?? 0) > 0) {
            return 'attachment:' . (string) $font['id'];
        }

        return 'name:' . strtolower((string) ($font['name'] ?? ''));
    }

    private function deriveFontNameFromFilePath(string $filePath): string
    {
        $stem = pathinfo(basename($filePath), PATHINFO_FILENAME);
        $stem = str_replace(['-', '_'], ' ', $stem);
        $stem = trim($stem);

        if ($stem === '') {
            return (string) $this->wpService->__('Untitled Font', 'municipio');
        }

        return ucwords($stem);
    }

    /**
     * @return array{name: string, slug: string, fontFamily: string, fontFace: array<int, array<string, mixed>>}|null
     */
    private function createActivatedFontFamily(int $fontFamilyPostId, string $fontName): ?array
    {
        if ($fontFamilyPostId <= 0 || trim($fontName) === '') {
            return null;
        }

        $fontFaces = $this->getActivatedFontFaces($fontFamilyPostId);

        if ($fontFaces === []) {
            return null;
        }

        return [
            'name' => trim($fontName),
            'slug' => $this->wpService->sanitizeTitle($fontName),
            'fontFamily' => sprintf('"%s", sans-serif', trim($fontName)),
            'fontFace' => $fontFaces,
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function getActivatedFontFaces(int $fontFamilyPostId): array
    {
        $fontFaces = $this->wpService->getPosts([
            'post_type' => 'wp_font_face',
            'post_status' => 'publish',
            'post_parent' => $fontFamilyPostId,
            'posts_per_page' => -1,
            'update_post_meta_cache' => false,
            'update_post_term_cache' => false,
        ]);

        $activatedFontFaces = [];

        foreach ($fontFaces as $fontFace) {
            $postContent = is_object($fontFace) && property_exists($fontFace, 'post_content') ? (string) $fontFace->post_content : (is_array($fontFace) && isset($fontFace['post_content']) ? (string) $fontFace['post_content'] : '');
            $fontFaceSettings = json_decode($postContent, true);

            if (!is_array($fontFaceSettings)) {
                continue;
            }

            $activatedFontFaces[] = $fontFaceSettings;
        }

        return $activatedFontFaces;
    }

    /**
     * @param array{name: string, slug: string, fontFamily: string, fontFace: array<int, array<string, mixed>>} $activatedFont
     */
    private function persistActivatedFontFamily(array $activatedFont): bool
    {
        if (!class_exists(\WP_Theme_JSON_Resolver::class) || !method_exists(\WP_Theme_JSON_Resolver::class, 'get_user_global_styles_post_id')) {
            return false;
        }

        $globalStylesPostId = \WP_Theme_JSON_Resolver::get_user_global_styles_post_id();

        if (!is_numeric($globalStylesPostId) || (int) $globalStylesPostId <= 0) {
            return false;
        }

        $globalStylesPostId = (int) $globalStylesPostId;
        $globalStylesPost = $this->wpService->getPost($globalStylesPostId);
        $postContent = is_object($globalStylesPost) && property_exists($globalStylesPost, 'post_content') ? (string) $globalStylesPost->post_content : (is_array($globalStylesPost) && isset($globalStylesPost['post_content']) ? (string) $globalStylesPost['post_content'] : '');
        $globalStylesData = json_decode($postContent, true);

        if (!is_array($globalStylesData)) {
            $globalStylesData = [];
        }

        $globalStylesData['version'] = isset($globalStylesData['version']) && is_int($globalStylesData['version']) ? $globalStylesData['version'] : (class_exists(\WP_Theme_JSON::class) ? \WP_Theme_JSON::LATEST_SCHEMA : 3);
        $globalStylesData['isGlobalStylesUserThemeJSON'] = true;

        $customFontFamilies = $globalStylesData['settings']['typography']['fontFamilies']['custom'] ?? [];
        $customFontFamilies = is_array($customFontFamilies) ? array_values(array_filter($customFontFamilies, 'is_array')) : [];

        foreach ($customFontFamilies as $index => $existingFontFamily) {
            if (($existingFontFamily['slug'] ?? null) !== $activatedFont['slug']) {
                continue;
            }

            $existingFontFaces = is_array($existingFontFamily['fontFace'] ?? null) ? array_values(array_filter($existingFontFamily['fontFace'], 'is_array')) : [];

            $customFontFamilies[$index] = [
                ...$existingFontFamily,
                ...$activatedFont,
                'fontFace' => $this->mergeActivatedFontFaces($existingFontFaces, $activatedFont['fontFace']),
            ];

            $globalStylesData['settings']['typography']['fontFamilies']['custom'] = $customFontFamilies;

            $this->wpService->wpUpdatePost([
                'ID' => $globalStylesPostId,
                'post_content' => $this->preparePostContent($globalStylesData),
            ]);

            return true;
        }

        $customFontFamilies[] = $activatedFont;
        $globalStylesData['settings']['typography']['fontFamilies']['custom'] = $customFontFamilies;

        $this->wpService->wpUpdatePost([
            'ID' => $globalStylesPostId,
            'post_content' => $this->preparePostContent($globalStylesData),
        ]);

        return true;
    }

    /**
     * @param array<int, array<string, mixed>> $existingFontFaces
     * @param array<int, array<string, mixed>> $activatedFontFaces
     *
     * @return array<int, array<string, mixed>>
     */
    private function mergeActivatedFontFaces(array $existingFontFaces, array $activatedFontFaces): array
    {
        $mergedFontFaces = [];

        foreach (array_merge($existingFontFaces, $activatedFontFaces) as $fontFace) {
            if (!is_array($fontFace)) {
                continue;
            }

            $mergedFontFaces[$this->createActivatedFontFaceKey($fontFace)] = $fontFace;
        }

        return array_values($mergedFontFaces);
    }

    /**
     * @param array<string, mixed> $fontFace
     */
    private function createActivatedFontFaceKey(array $fontFace): string
    {
        $fontStyle = isset($fontFace['fontStyle']) ? strtolower(trim((string) $fontFace['fontStyle'])) : 'normal';
        $fontWeight = isset($fontFace['fontWeight']) ? trim((string) $fontFace['fontWeight']) : '400';
        $sources = isset($fontFace['src']) && is_array($fontFace['src']) ? array_values(array_filter($fontFace['src'], 'is_string')) : [];

        return $fontStyle . '|' . $fontWeight . '|' . implode(',', $sources);
    }

    /**
     * @param array<string, mixed> $postContent
     */
    private function preparePostContent(array $postContent): string
    {
        $json = $this->wpService->wpJsonEncode($postContent);

        if (is_string($json) && $json !== '') {
            $slashedJson = $this->wpService->wpSlash($json);

            if (is_string($slashedJson)) {
                return $slashedJson;
            }
        }

        return addslashes(is_string($json) ? $json : (string) json_encode($postContent));
    }
}
