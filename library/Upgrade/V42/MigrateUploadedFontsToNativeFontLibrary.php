<?php

declare(strict_types=1);

namespace Municipio\Upgrade\V42;

use Closure;
use WpService\WpService;

/**
 * Migrates previously uploaded fonts to the native WordPress font library.
 */
class MigrateUploadedFontsToNativeFontLibrary
{
    use InteractsWithNativeFontLibrary;

    protected function getWpService(): WpService
    {
        return $this->wpService;
    }

    private const LEGACY_UPLOADED_FONTS_SETTING = 'municipio_font_catalog_uploaded_fonts';

    public const MIGRATION_SETTING = 'municipio_native_font_library_uploaded_fonts_migrated';

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
     * @var Closure(): array<int, array{id: int, name: string, type: string, url: string}>
     */
    private readonly Closure $uploadedFontsProvider;

    /**
     * @var Closure(array{id: int, name: string, type: string, url: string}): array{source: string, fontFile: string|null}|null
     */
    private readonly Closure $fontSourceResolver;

    public function __construct(
        private readonly WpService $wpService,
        ?Closure $uploadedFontsProvider = null,
        ?Closure $fontSourceResolver = null,
    ) {
        $this->uploadedFontsProvider = $uploadedFontsProvider ?? fn(): array => $this->getUploadedFonts();
        $this->fontSourceResolver = $fontSourceResolver ?? fn(array $font): ?array => $this->resolveNativeFontSource($font);
    }

    /**
     * Migrates uploaded fonts into native font-family and font-face posts.
     */
    public function migrate(): void
    {
        if ((bool) $this->wpService->getThemeMod(self::MIGRATION_SETTING, false)) {
            return;
        }

        if (!$this->nativeFontLibraryIsAvailable()) {
            return;
        }

        foreach (($this->uploadedFontsProvider)() as $font) {
            if (($font['name'] ?? '') === '' || ($font['url'] ?? '') === '') {
                continue;
            }

            $fontFamilyPostId = $this->createNativeFontFamilyIfMissing((string) $font['name']);

            if ($fontFamilyPostId === null) {
                continue;
            }

            $resolvedSource = ($this->fontSourceResolver)($font);

            if (!is_array($resolvedSource) || ($resolvedSource['source'] ?? '') === '') {
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
        }

        $this->wpService->setThemeMod(self::MIGRATION_SETTING, true);
    }

    /**
     * Returns uploaded fonts from the legacy media-library flow and managed upload rows.
     *
     * @return array<int, array{id: int, name: string, type: string, url: string}>
     */
    private function getUploadedFonts(): array
    {
        $fonts = [];

        foreach ($this->getManagedFonts() as $font) {
            $fonts[$this->createFontKey($font)] = $font;
        }

        foreach ($this->getLegacyAttachmentFonts() as $font) {
            $fonts[$this->createFontKey($font)] = $font;
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
            'post_mime_type' => $this->allowedMimes,
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

        $fileName = $preferredFileName;

        if (!is_string($fileName) || $fileName === '') {
            $fileName = basename($sourceRealPath);
        }

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
        return sprintf('%s|%s', $font['name'], $font['url']);
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
}
