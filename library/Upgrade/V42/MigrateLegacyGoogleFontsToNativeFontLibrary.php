<?php

declare(strict_types=1);

namespace Municipio\Upgrade\V42;

use Closure;
use WpService\WpService;

/**
 * Detects previously used legacy Google fonts and installs them through
 * WordPress' native font collection data.
 */
class MigrateLegacyGoogleFontsToNativeFontLibrary
{
    use InteractsWithNativeFontLibrary;

    protected function getWpService(): WpService
    {
        return $this->wpService;
    }

    public const MIGRATION_SETTING = 'municipio_native_font_library_legacy_google_fonts_migrated';
    public const ACTIVATION_SETTING = 'municipio_native_font_library_legacy_google_fonts_activated';
    public const LOCAL_INSTALL_SETTING = 'municipio_native_font_library_legacy_google_fonts_locally_installed';
    public const LEGACY_GOOGLE_FONTS_SETTING = 'municipio_font_catalog_google_fonts';

    /**
     * Known typography settings containing legacy font family selections.
     *
     * @var array<int, string>
     */
    private const FONT_SETTING_KEYS = [
        'typography_base',
        'typography_heading',
        'typography_bold',
        'typography_italic',
        'typography_lead',
        'header_brand_font_settings',
    ];

    /**
     * Default variants that should be active after Google-font migration.
     *
     * @var array<int, string>
     */
    private const DEFAULT_ACTIVATED_VARIANTS = [
        '400',
        '500',
        '600',
        '700',
        '400italic',
        '500italic',
        '600italic',
        '700italic',
    ];

    /**
     * Default variants by typography setting when the theme mod has no explicit variant.
     *
     * @var array<string, string>
     */
    private const FONT_SETTING_DEFAULT_VARIANTS = [
        'typography_base' => 'regular',
        'typography_heading' => '700',
        'typography_bold' => '700',
        'typography_italic' => 'italic',
        'typography_lead' => '500',
        'header_brand_font_settings' => 'regular',
    ];

    /**
     * @var Closure(): array<string, array{name: string, fontFamily: string, fontFace: array<int, array<string, mixed>>, preview?: string}>
     */
    private readonly Closure $installableFontsProvider;

    /**
     * @var Closure(array{name: string, slug: string, fontFamily: string, fontFace: array<int, array<string, mixed>>, preview?: string}): void
     */
    private readonly Closure $fontActivator;

    public function __construct(
        private readonly WpService $wpService,
        ?Closure $installableFontsProvider = null,
        ?Closure $fontActivator = null,
    ) {
        $this->installableFontsProvider = $installableFontsProvider ?? fn(): array => $this->getInstallableFontsFromWordPressCollections();
        $this->fontActivator = $fontActivator ?? function (array $font): void {
            $this->persistActivatedFontFamily($font);
        };
    }

    /**
     * Installs previously used Google fonts into native font-family and font-face posts,
     * then activates supported variants in user global styles.
     */
    public function migrate(): void
    {
        if (!$this->nativeFontLibraryIsAvailable()) {
            return;
        }

        $fonts = $this->getPreviouslyUsedInstallableFonts(($this->installableFontsProvider)());
        $fontsToActivate = $fonts;

        if (!(bool) $this->wpService->getThemeMod(self::LOCAL_INSTALL_SETTING, false)) {
            $fontsToActivate = [];

            foreach ($fonts as $font) {
                $installedFont = $this->installFont($font);

                if ($installedFont !== null) {
                    $fontsToActivate[] = $installedFont;
                }
            }

            $this->wpService->setThemeMod(self::LOCAL_INSTALL_SETTING, true);
        }

        if (!(bool) $this->wpService->getThemeMod(self::MIGRATION_SETTING, false)) {
            $this->wpService->setThemeMod(self::MIGRATION_SETTING, true);
        }

        if ((bool) $this->wpService->getThemeMod(self::ACTIVATION_SETTING, false) && $fontsToActivate === $fonts) {
            return;
        }

        foreach ($fontsToActivate as $font) {
            $activatedFont = $this->createActivatedFontFamily($font);

            if ($activatedFont === null) {
                continue;
            }

            ($this->fontActivator)($activatedFont);
        }

        $this->wpService->setThemeMod(self::ACTIVATION_SETTING, true);
    }

    /**
     * @param array{name: string, fontFamily: string, fontFace: array<int, array<string, mixed>>, preview?: string} $font
     */
    private function installFont(array $font): ?array
    {
        $fontFamilyPostId = $this->createNativeFontFamilyIfMissing($font['name'], $font['fontFamily'], $font['preview'] ?? null);

        if ($fontFamilyPostId === null) {
            return null;
        }

        $this->removeRemoteFontFaces($fontFamilyPostId);

        $installedFontFaces = [];

        foreach ($font['fontFace'] as $fontFace) {
            $installedFontFace = $this->installFontFace($fontFace);

            if ($installedFontFace === null) {
                continue;
            }

            $this->createNativeFontFaceIfMissing(
                $fontFamilyPostId,
                $font['name'],
                $installedFontFace['src'],
                isset($installedFontFace['fontStyle']) ? (string) $installedFontFace['fontStyle'] : 'normal',
                isset($installedFontFace['fontWeight']) ? (string) $installedFontFace['fontWeight'] : '400',
                $installedFontFace['fontFile'] ?? null,
                isset($installedFontFace['unicodeRange']) && is_string($installedFontFace['unicodeRange']) ? $installedFontFace['unicodeRange'] : null,
                isset($installedFontFace['preview']) && is_string($installedFontFace['preview']) ? $installedFontFace['preview'] : null,
            );

            unset($installedFontFace['fontFile']);
            $installedFontFaces[] = $installedFontFace;
        }

        if ($installedFontFaces === []) {
            return null;
        }

        $installedFont = [
            'name' => $font['name'],
            'fontFamily' => $font['fontFamily'],
            'fontFace' => $installedFontFaces,
        ];

        if (isset($font['preview']) && is_string($font['preview']) && trim($font['preview']) !== '') {
            $installedFont['preview'] = trim($font['preview']);
        }

        return $installedFont;
    }

    private function removeRemoteFontFaces(int $fontFamilyPostId): void
    {
        $fontDir = $this->wpService->wpGetFontDir();
        $baseUrl = isset($fontDir['baseurl']) && is_string($fontDir['baseurl']) ? rtrim($fontDir['baseurl'], '/') : '';

        if ($fontFamilyPostId <= 0 || $baseUrl === '') {
            return;
        }

        $fontFaces = $this->wpService->getPosts([
            'post_type' => 'wp_font_face',
            'post_status' => 'publish',
            'post_parent' => $fontFamilyPostId,
            'posts_per_page' => -1,
            'update_post_meta_cache' => false,
            'update_post_term_cache' => false,
        ]);

        foreach ($fontFaces as $fontFace) {
            $fontFaceId = is_object($fontFace) && property_exists($fontFace, 'ID') ? (int) $fontFace->ID : 0;
            $postContent = is_object($fontFace) && property_exists($fontFace, 'post_content') ? (string) $fontFace->post_content : '';
            $fontFaceSettings = json_decode($postContent, true);
            $fontFaceSources = is_array($fontFaceSettings) ? $this->normalizeSources($fontFaceSettings['src'] ?? []) : [];

            if ($fontFaceId <= 0 || $fontFaceSources === [] || $this->hasOnlyLocalFontSources($fontFaceSources, $baseUrl)) {
                continue;
            }

            $this->wpService->wpDeletePost($fontFaceId, true);
        }
    }

    /**
     * @param array<int, string> $fontFaceSources
     */
    private function hasOnlyLocalFontSources(array $fontFaceSources, string $baseUrl): bool
    {
        foreach ($fontFaceSources as $fontFaceSource) {
            if (!str_starts_with($fontFaceSource, $baseUrl)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Downloads a font face into the WordPress fonts directory and returns the local settings.
     *
     * @param array<string, mixed> $fontFace
     *
     * @return array<string, mixed>|null
     */
    private function installFontFace(array $fontFace): ?array
    {
        $sources = $this->normalizeSources($fontFace['src'] ?? []);

        if ($sources === []) {
            return null;
        }

        $installedSources = [];
        $fontFile = null;

        foreach ($sources as $source) {
            $installedSource = $this->installFontSource($source);

            if ($installedSource === null) {
                continue;
            }

            $installedSources[] = $installedSource['url'];
            $fontFile ??= $installedSource['fontFile'];
        }

        if ($installedSources === []) {
            return null;
        }

        $fontFace['src'] = $installedSources;

        if ($fontFile !== null) {
            $fontFace['fontFile'] = $fontFile;
        }

        return $fontFace;
    }

    /**
     * Downloads one remote font source and moves it into the native WordPress font directory.
     *
     * @return array{url: string, fontFile: string}|null
     */
    private function installFontSource(string $source): ?array
    {
        if ($source === '') {
            return null;
        }

        $this->ensureDownloadUrlIsAvailable();

        $temporaryFile = $this->wpService->downloadUrl($source);

        if (!is_string($temporaryFile) || $temporaryFile === '') {
            return null;
        }

        $fileName = $this->getDownloadedFontFileName($source, $temporaryFile);
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
            'url' => (string) $sideloadedFile['url'],
            'fontFile' => $this->relativeFontsPath((string) $sideloadedFile['file']),
        ];
    }

    private function ensureDownloadUrlIsAvailable(): void
    {
        if (function_exists('download_url') || !defined('ABSPATH')) {
            return;
        }

        require_once ABSPATH . 'wp-admin/includes/file.php';
    }

    private function getDownloadedFontFileName(string $source, string $temporaryFile): string
    {
        $path = (string) parse_url($source, PHP_URL_PATH);
        $fileName = basename($path);

        if ($fileName !== '' && $fileName !== '/' && $fileName !== '.') {
            return $fileName;
        }

        $extension = pathinfo($temporaryFile, PATHINFO_EXTENSION);

        return $extension !== '' ? 'font.' . $extension : 'font-file';
    }

    private function relativeFontsPath(string $path): string
    {
        $fontDir = $this->wpService->wpGetFontDir();
        $baseDir = isset($fontDir['basedir']) && is_string($fontDir['basedir']) ? rtrim($fontDir['basedir'], '/') : '';

        if ($baseDir !== '' && str_starts_with($path, $baseDir)) {
            return ltrim(substr($path, strlen($baseDir)), '/');
        }

        return $path;
    }

    /**
     * @param array<string, array{name: string, fontFamily: string, fontFace: array<int, array<string, mixed>>, preview?: string}> $installableFonts
     *
     * @return array<string, array{name: string, fontFamily: string, fontFace: array<int, array<string, mixed>>, preview?: string}>
     */
    private function getPreviouslyUsedInstallableFonts(array $installableFonts): array
    {
        $installableFonts = $this->normalizeInstallableFontsIndex($installableFonts);
        $catalogFonts = $this->wpService->getThemeMod(self::LEGACY_GOOGLE_FONTS_SETTING, []);
        $catalogFonts = is_array($catalogFonts) ? array_values(array_filter(array_map('strval', $catalogFonts))) : [];

        $selectedFamilies = array_values(array_unique(array_merge(
            $this->getSelectedFontFamiliesFromThemeMods(),
            $catalogFonts,
        )));

        $fontsToInstall = [];

        foreach ($selectedFamilies as $fontFamily) {
            $fontKey = $this->normalizeFontFamilyName($fontFamily);

            if (!isset($installableFonts[$fontKey])) {
                continue;
            }

            $fontsToInstall[$fontKey] = $installableFonts[$fontKey];
        }

        ksort($fontsToInstall);

        return $fontsToInstall;
    }

    /**
     * @param array<string, array{name: string, fontFamily: string, fontFace: array<int, array<string, mixed>>, preview?: string}> $installableFonts
     *
     * @return array<string, array{name: string, fontFamily: string, fontFace: array<int, array<string, mixed>>, preview?: string}>
     */
    private function normalizeInstallableFontsIndex(array $installableFonts): array
    {
        $normalizedFonts = [];

        foreach ($installableFonts as $fontKey => $font) {
            if (!is_array($font)) {
                continue;
            }

            $normalizedKey = $this->normalizeFontFamilyName((string) $fontKey);

            if ($normalizedKey === '' && isset($font['name']) && is_string($font['name'])) {
                $normalizedKey = $this->normalizeFontFamilyName($font['name']);
            }

            if ($normalizedKey === '') {
                continue;
            }

            $normalizedFonts[$normalizedKey] = $font;
        }

        return $normalizedFonts;
    }

    /**
     * @return array<int, string>
     */
    private function getSelectedFontFamiliesFromThemeMods(): array
    {
        return array_keys($this->getSelectedFontVariantsFromThemeMods());
    }

    /**
     * @return array<string, array<int, string>>
     */
    private function getSelectedFontVariantsFromThemeMods(): array
    {
        $fonts = [];

        foreach (self::FONT_SETTING_KEYS as $settingKey) {
            $value = $this->wpService->getThemeMod($settingKey, []);

            if (!is_array($value) || !array_key_exists('font-family', $value) || $value['font-family'] === '') {
                continue;
            }

            $fontFamily = $this->normalizeFontFamilyName((string) $value['font-family']);
            $variant = $this->normalizeVariant(isset($value['variant']) && is_string($value['variant']) && $value['variant'] !== '' ? $value['variant'] : self::FONT_SETTING_DEFAULT_VARIANTS[$settingKey] ?? 'regular');

            if ($fontFamily === '' || $variant === null) {
                continue;
            }

            $fonts[$fontFamily] ??= [];

            if (!in_array($variant, $fonts[$fontFamily], true)) {
                $fonts[$fontFamily][] = $variant;
            }
        }

        return $fonts;
    }

    /**
     * @param array{name: string, fontFamily: string, fontFace: array<int, array<string, mixed>>, preview?: string} $font
     *
     * @return array{name: string, slug: string, fontFamily: string, fontFace: array<int, array<string, mixed>>, preview?: string}|null
     */
    private function createActivatedFontFamily(array $font): ?array
    {
        $activatedFontFaces = $this->getActivatedFontFaces($font);

        if ($activatedFontFaces === []) {
            return null;
        }

        $activatedFont = [
            'name' => $font['name'],
            'slug' => $this->createFontSlug($font['name']),
            'fontFamily' => $font['fontFamily'],
            'fontFace' => $activatedFontFaces,
        ];

        if (isset($font['preview']) && is_string($font['preview']) && trim($font['preview']) !== '') {
            $activatedFont['preview'] = trim($font['preview']);
        }

        return $activatedFont;
    }

    /**
     * @param array{name: string, fontFamily: string, fontFace: array<int, array<string, mixed>>, preview?: string} $font
     *
     * @return array<int, array<string, mixed>>
     */
    private function getActivatedFontFaces(array $font): array
    {
        $fontKey = $this->normalizeFontFamilyName($font['name']);
        $selectedVariants = $this->getSelectedFontVariantsFromThemeMods()[$fontKey] ?? [];
        $allowedVariants = array_fill_keys(array_merge(self::DEFAULT_ACTIVATED_VARIANTS, $selectedVariants), true);

        return array_values(array_filter(
            $font['fontFace'],
            fn(array $fontFace): bool => isset($allowedVariants[$this->createFontFaceVariantKey($fontFace)]),
        ));
    }

    /**
     * @param array{name: string, slug: string, fontFamily: string, fontFace: array<int, array<string, mixed>>, preview?: string} $activatedFont
     */
    private function persistActivatedFontFamily(array $activatedFont): void
    {
        $globalStylesPostId = $this->getGlobalStylesPostId();

        if ($globalStylesPostId === null) {
            return;
        }

        $globalStylesPost = $this->wpService->getPost($globalStylesPostId);
        $postContent = is_object($globalStylesPost) && property_exists($globalStylesPost, 'post_content') ? (string) $globalStylesPost->post_content : (is_array($globalStylesPost) && isset($globalStylesPost['post_content']) ? (string) $globalStylesPost['post_content'] : '');

        $globalStylesData = $this->decodeGlobalStylesPostContent($postContent);
        $customFontFamilies = $globalStylesData['settings']['typography']['fontFamilies']['custom'] ?? [];
        $customFontFamilies = is_array($customFontFamilies) ? array_values(array_filter($customFontFamilies, 'is_array')) : [];

        $fontWasUpdated = false;

        foreach ($customFontFamilies as $index => $existingFontFamily) {
            if (($existingFontFamily['slug'] ?? null) !== $activatedFont['slug']) {
                continue;
            }

            $customFontFamilies[$index] = $this->mergeActivatedFontFamily($existingFontFamily, $activatedFont);
            $fontWasUpdated = true;
            break;
        }

        if (!$fontWasUpdated) {
            $customFontFamilies[] = $activatedFont;
        }

        $globalStylesData['settings']['typography']['fontFamilies']['custom'] = $customFontFamilies;

        $this->wpService->wpUpdatePost([
            'ID' => $globalStylesPostId,
            'post_content' => $this->preparePostContent($globalStylesData),
        ]);
    }

    /**
     * @param array<string, mixed> $existingFontFamily
     * @param array{name: string, slug: string, fontFamily: string, fontFace: array<int, array<string, mixed>>, preview?: string} $activatedFontFamily
     *
     * @return array<string, mixed>
     */
    private function mergeActivatedFontFamily(array $existingFontFamily, array $activatedFontFamily): array
    {
        $mergedFontFaces = [];

        foreach (array_merge($existingFontFamily['fontFace'] ?? [], $activatedFontFamily['fontFace']) as $fontFace) {
            if (!is_array($fontFace)) {
                continue;
            }

            $mergedFontFaces[$this->createFontFaceVariantKey($fontFace)] = $fontFace;
        }

        return [
            ...$existingFontFamily,
            ...$activatedFontFamily,
            'fontFace' => array_values($mergedFontFaces),
        ];
    }

    private function getGlobalStylesPostId(): ?int
    {
        if (!class_exists(\WP_Theme_JSON_Resolver::class) || !method_exists(\WP_Theme_JSON_Resolver::class, 'get_user_global_styles_post_id')) {
            return null;
        }

        $postId = \WP_Theme_JSON_Resolver::get_user_global_styles_post_id();

        return is_numeric($postId) && (int) $postId > 0 ? (int) $postId : null;
    }

    /**
     * @param array<string, mixed> $fontFace
     */
    private function createFontFaceVariantKey(array $fontFace): string
    {
        $fontWeight = isset($fontFace['fontWeight']) ? $this->normalizeFontWeight((string) $fontFace['fontWeight']) : '400';
        $fontStyle = isset($fontFace['fontStyle']) ? strtolower(trim((string) $fontFace['fontStyle'])) : 'normal';

        return $fontStyle === 'italic' ? $fontWeight . 'italic' : $fontWeight;
    }

    private function normalizeVariant(string $variant): ?string
    {
        $variant = strtolower(trim($variant));

        return match ($variant) {
            'regular', 'normal' => '400',
            'italic' => '400italic',
            default => preg_match('/^\d+(italic)?$/', $variant) === 1 ? $variant : null,
        };
    }

    private function normalizeFontWeight(string $fontWeight): string
    {
        $fontWeight = strtolower(trim($fontWeight));

        return match ($fontWeight) {
            'normal', 'regular' => '400',
            'bold' => '700',
            default => preg_match('/^\d+$/', $fontWeight) === 1 ? $fontWeight : '400',
        };
    }

    private function createFontSlug(string $fontName): string
    {
        return $this->wpService->sanitizeTitle($fontName);
    }

    /**
     * @param string $postContent
     *
     * @return array<string, mixed>
     */
    private function decodeGlobalStylesPostContent(string $postContent): array
    {
        $decodedPostContent = json_decode($postContent, true);

        if (!is_array($decodedPostContent)) {
            $decodedPostContent = [];
        }

        $decodedPostContent['version'] = isset($decodedPostContent['version']) && is_int($decodedPostContent['version']) ? $decodedPostContent['version'] : (class_exists(\WP_Theme_JSON::class) ? \WP_Theme_JSON::LATEST_SCHEMA : 3);
        $decodedPostContent['isGlobalStylesUserThemeJSON'] = true;

        return $decodedPostContent;
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

    /**
     * @return array<string, array{name: string, fontFamily: string, fontFace: array<int, array<string, mixed>>, preview?: string}>
     */
    private function getInstallableFontsFromWordPressCollections(): array
    {
        if (!class_exists(\WP_Font_Library::class) || !method_exists(\WP_Font_Library::class, 'get_instance')) {
            return [];
        }

        $fontLibrary = \WP_Font_Library::get_instance();

        if (!is_object($fontLibrary) || !method_exists($fontLibrary, 'get_font_collections')) {
            return [];
        }

        $installableFonts = [];

        foreach ((array) $fontLibrary->get_font_collections() as $collection) {
            if (!is_object($collection) || !method_exists($collection, 'get_data')) {
                continue;
            }

            $collectionData = $collection->get_data();

            if ($this->wpService->isWpError($collectionData) || !is_array($collectionData)) {
                continue;
            }

            foreach ($collectionData['font_families'] ?? [] as $fontDefinition) {
                if (!is_array($fontDefinition)) {
                    continue;
                }

                $font = $this->normalizeInstallableFont($fontDefinition);

                if ($font === null) {
                    continue;
                }

                foreach ($this->createInstallableFontKeys($fontDefinition, $font) as $fontKey) {
                    $installableFonts[$fontKey] ??= $font;
                }
            }
        }

        return $installableFonts;
    }

    /**
     * @param array<string, mixed> $fontDefinition
     *
     * @return array{name: string, fontFamily: string, fontFace: array<int, array<string, mixed>>, preview?: string}|null
     */
    private function normalizeInstallableFont(array $fontDefinition): ?array
    {
        $fontFamilySettings = $fontDefinition['font_family_settings'] ?? null;

        if (!is_array($fontFamilySettings)) {
            return null;
        }

        $fontFamilyCssValue = isset($fontFamilySettings['fontFamily']) ? trim((string) $fontFamilySettings['fontFamily']) : '';
        $fontFaces = isset($fontFamilySettings['fontFace']) && is_array($fontFamilySettings['fontFace']) ? array_values(array_filter($fontFamilySettings['fontFace'], 'is_array')) : [];
        $fontName = isset($fontFamilySettings['name']) ? trim((string) $fontFamilySettings['name']) : '';

        if ($fontName === '') {
            $fontName = $this->normalizeFontFamilyName($fontFamilyCssValue);
        }

        if ($fontName === '' || $fontFamilyCssValue === '' || $fontFaces === []) {
            return null;
        }

        $font = [
            'name' => $fontName,
            'fontFamily' => $fontFamilyCssValue,
            'fontFace' => $fontFaces,
        ];

        if (isset($fontFamilySettings['preview']) && is_string($fontFamilySettings['preview']) && trim($fontFamilySettings['preview']) !== '') {
            $font['preview'] = trim($fontFamilySettings['preview']);
        }

        return $font;
    }

    /**
     * @param array<string, mixed> $fontDefinition
     * @param array{name: string, fontFamily: string, fontFace: array<int, array<string, mixed>>, preview?: string} $font
     *
     * @return array<int, string>
     */
    private function createInstallableFontKeys(array $fontDefinition, array $font): array
    {
        $fontFamilySettings = $fontDefinition['font_family_settings'] ?? [];
        $fontKeys = [
            $this->normalizeFontFamilyName($font['name']),
            $this->normalizeFontFamilyName($font['fontFamily']),
            isset($fontFamilySettings['slug']) ? trim((string) $fontFamilySettings['slug']) : '',
        ];

        return array_values(array_unique(array_filter($fontKeys)));
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

    private function normalizeFontFamilyName(string $fontFamily): string
    {
        $fontFamily = trim(strtok($fontFamily, ','));

        return strtolower(trim($fontFamily, " \t\n\r\0\x0B\"'"));
    }
}
