<?php

declare(strict_types=1);

namespace Municipio\Upgrade\V42;

use Closure;
use Municipio\Customizer\Fonts\NativeFontLibraryRepository;
use WpService\WpService;

/**
 * Migrates previously uploaded fonts to the native WordPress font library.
 */
class MigrateUploadedFontsToNativeFontLibrary
{
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
        private readonly NativeFontLibraryRepository $nativeFontLibraryRepository,
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

        if (!$this->nativeFontLibraryRepository->isAvailable()) {
            return;
        }

        foreach (($this->uploadedFontsProvider)() as $font) {
            if (($font['name'] ?? '') === '' || ($font['url'] ?? '') === '') {
                continue;
            }

            $fontFamilyPostId = $this->nativeFontLibraryRepository->createFontFamilyIfMissing((string) $font['name']);

            if ($fontFamilyPostId === null) {
                continue;
            }

            $resolvedSource = ($this->fontSourceResolver)($font);

            if (!is_array($resolvedSource) || ($resolvedSource['source'] ?? '') === '') {
                continue;
            }

            $this->nativeFontLibraryRepository->createFontFaceIfMissing(
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
        $sourceFromAttachment = $font['id'] > 0 ? $this->copyAttachmentToFontDirectory($font['id']) : null;

        if ($sourceFromAttachment !== null) {
            return $sourceFromAttachment;
        }

        $sourceFromUrl = $this->copyUrlToFontDirectory($font['url']);

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
    private function copyAttachmentToFontDirectory(int $attachmentId): ?array
    {
        if (!function_exists('get_attached_file')) {
            return null;
        }

        $filePath = get_attached_file($attachmentId);

        if (!is_string($filePath) || $filePath === '') {
            return null;
        }

        return $this->copyFileToFontDirectory($filePath);
    }

    /**
     * @return array{source: string, fontFile: string|null}|null
     */
    private function copyUrlToFontDirectory(string $url): ?array
    {
        if ($url === '' || !function_exists('wp_get_font_dir')) {
            return null;
        }

        $fontDir = wp_get_font_dir();

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

        if (!function_exists('wp_upload_dir')) {
            return null;
        }

        $uploadDir = wp_upload_dir();

        if (!is_array($uploadDir) || empty($uploadDir['baseurl']) || empty($uploadDir['basedir'])) {
            return null;
        }

        $baseUrl = rtrim((string) $uploadDir['baseurl'], '/');

        if (!str_starts_with($url, $baseUrl . '/')) {
            return null;
        }

        $relativePath = ltrim(substr($url, strlen($baseUrl)), '/');
        $filePath = rtrim((string) $uploadDir['basedir'], DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relativePath);

        return $this->copyFileToFontDirectory($filePath);
    }

    /**
     * @return array{source: string, fontFile: string|null}|null
     */
    private function copyFileToFontDirectory(string $sourcePath): ?array
    {
        if ($sourcePath === '' || !is_readable($sourcePath) || !function_exists('wp_get_font_dir')) {
            return null;
        }

        $fontDir = wp_get_font_dir();

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

        if (function_exists('wp_mkdir_p')) {
            wp_mkdir_p($fontBaseDir);
        } elseif (!is_dir($fontBaseDir)) {
            mkdir($fontBaseDir, 0777, true);
        }

        if (!is_dir($fontBaseDir) || !is_writable($fontBaseDir)) {
            return null;
        }

        $fileName = basename($sourceRealPath);

        if (function_exists('wp_unique_filename')) {
            $fileName = wp_unique_filename($fontBaseDir, $fileName);
        }

        $destinationPath = $fontBaseDir . DIRECTORY_SEPARATOR . $fileName;

        if (!file_exists($destinationPath) && !copy($sourceRealPath, $destinationPath)) {
            return null;
        }

        return [
            'source' => $fontBaseUrl . '/' . $fileName,
            'fontFile' => $fileName,
        ];
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
