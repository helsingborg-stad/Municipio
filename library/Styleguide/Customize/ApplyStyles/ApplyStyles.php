<?php

declare(strict_types=1);

namespace Municipio\Styleguide\Customize\ApplyStyles;

use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\AddAction;
use WpService\Contracts\AddFilter;
use WpService\Contracts\GetThemeMod;
use WpService\Contracts\WpAddInlineStyle;
use WpService\Contracts\WpEnqueueStyle;
use WpService\Contracts\WpRegisterStyle;

class ApplyStyles implements Hookable
{
    private const STYLE_HANDLE = 'styleguide-design-builder-output';
    private const EDITOR_ROOT_SELECTOR = ':root :where(.editor-styles-wrapper)';
    private const DARK_SCHEME_QUERY = '(prefers-color-scheme: dark)';
    private const CONTRAST_SCHEME_QUERY = '(prefers-contrast: more), (forced-colors: active)';

    public function __construct(
        private AddAction&AddFilter&WpRegisterStyle&WpEnqueueStyle&WpAddInlineStyle&GetThemeMod $wpService,
        private EditorColorTokens $editorColorTokens = new EditorColorTokens(),
    ) {}

    public function addHooks(): void
    {
        $this->wpService->addAction('wp_enqueue_scripts', [$this, 'applyStyles']);
        $this->wpService->addAction('acf/input/admin_enqueue_scripts', [$this, 'applyPaletteStyles']);
        $this->wpService->addFilter('block_editor_settings_all', [$this, 'applyEditorIframeStyles'], 10, 2);
    }

    public function applyStyles(): void
    {
        $styles = $this->getTokensAsCss();
        $this->applyInlineStyles($styles);
    }

    /** Keep admin swatches in the same colour scheme as the content, without styling the admin itself. */
    public function applyPaletteStyles(): void
    {
        $css = (new PaletteColorTokens())->getCss($this->getStoredTokens(), [
            '_dark' => self::DARK_SCHEME_QUERY,
            '_contrast' => self::CONTRAST_SCHEME_QUERY,
        ]);
        $handle = 'municipio-palette-color-tokens';
        $this->wpService->wpRegisterStyle($handle, false);
        $this->wpService->wpEnqueueStyle($handle);
        $this->wpService->wpAddInlineStyle($handle, $css);
    }

    /**
     * Adds stored design tokens to the Gutenberg content iframe styles.
     *
     * @param array<string, mixed> $settings
     * @param mixed                $context
     *
     * @return array<string, mixed>
     */
    public function applyEditorIframeStyles(array $settings, mixed $context = null): array
    {
        $styles = $this->getTokensAsCss(self::EDITOR_ROOT_SELECTOR);

        if ($styles === '') {
            return $settings;
        }

        $editorStyles = $settings['styles'] ?? [];

        if (!is_array($editorStyles)) {
            $editorStyles = [];
        }

        $editorStyles[] = ['css' => $styles];
        $settings['styles'] = $editorStyles;

        return $settings;
    }

    private function getTokensAsCss(string $editorSelector = ''): string
    {
        $storedTokens = $this->getStoredTokens();
        $tokens = array_merge($storedTokens['token'], $storedTokens['component']);
        $converter = new DesignTokensToCssConverter\DesignTokensToCssConverter();
        $css = $converter->convert($tokens);

        return (
            '@layer theme {'
            . $css
            . $this->editorColorTokens->getCss($storedTokens['token'], $editorSelector)
            . $this->getColorSchemeStyles($converter, $storedTokens['_dark'], self::DARK_SCHEME_QUERY, $editorSelector, $storedTokens, 'dark')
            // High contrast deliberately follows dark mode when both preferences match.
            . $this->getColorSchemeStyles($converter, $storedTokens['_contrast'], self::CONTRAST_SCHEME_QUERY, $editorSelector, $storedTokens, 'contrast')
            . '}'
        );
    }

    /**
     * Generates root and editor-wrapper overrides for one browser preference.
     *
     * @param array<string, mixed>|null $tokens
     */
    private function getColorSchemeStyles(
        DesignTokensToCssConverter\DesignTokensToCssConverter $converter,
        ?array $tokens,
        string $mediaQuery,
        string $editorSelector,
        array $storedTokens,
        string $scheme,
    ): string {
        $enabled = $tokens !== null;
        $tokens = ColorSchemeOverrides::filter($tokens);
        $components = $enabled ? ($storedTokens['componentSchemes'][$scheme] ?? []) : [];
        $hasComponentVariants = $enabled && array_filter($storedTokens['componentSchemes']) !== [];

        if ($tokens === [] && !$hasComponentVariants) {
            return '';
        }

        $rootStyles = $converter->convertRootTokens($tokens);
        $editorStyles = $this->editorColorTokens->getCss($tokens, $editorSelector);
        // Repeat inherited scope assignments too: a scheme's general component
        // colour must not accidentally override a more specific Default scope.
        $effectiveComponents = array_replace_recursive($storedTokens['component'], $components);
        foreach ($enabled ? $storedTokens['componentSchemes'] : [] as $variant) {
            foreach ($variant as $scope => $componentMap) {
                foreach ($componentMap as $component => $variables) {
                    foreach ($variables as $variable => $value) {
                        $effectiveComponents[$scope][$component][$variable] ??= $effectiveComponents['__general__'][$component][$variable] ?? 'initial';
                    }
                }
            }
        }
        $componentStyles = !$hasComponentVariants ? '' : $converter->convert($effectiveComponents);

        return sprintf('@media %s {%s%s%s}', $mediaQuery, $rootStyles, $editorStyles, $componentStyles);
    }

    private function getStoredTokens(): array
    {
        $stored = $this->wpService->getThemeMod('tokens');
        return $this->sanitizeStoredValue($stored);
    }

    private function sanitizeStoredValue(mixed $value): array
    {
        $default = ['token' => [], '_dark' => null, '_contrast' => null, 'component' => [], 'componentSchemes' => []];

        if (!is_string($value)) {
            return $default;
        }

        $decoded = json_decode($value, true);

        if (!is_array($decoded) || !is_array($decoded['token'] ?? null) || !is_array($decoded['component'] ?? null)) {
            return $default;
        }

        return [
            'token' => ColorSchemeOverrides::filterSafeRootTokens($decoded['token']),
            '_dark' => is_array($decoded['_dark'] ?? null) ? ColorSchemeOverrides::filter($decoded['_dark']) : null,
            '_contrast' => is_array($decoded['_contrast'] ?? null) ? ColorSchemeOverrides::filter($decoded['_contrast']) : null,
            'component' => $decoded['component'],
            'componentSchemes' => [
                'dark' => ColorSchemeOverrides::filterComponents($decoded['componentSchemes']['dark'] ?? null),
                'contrast' => ColorSchemeOverrides::filterComponents($decoded['componentSchemes']['contrast'] ?? null),
            ],
        ];
    }

    private function applyInlineStyles(string $styles): void
    {
        $this->wpService->wpRegisterStyle(self::STYLE_HANDLE, false); // Not applying source since inline styles are the only goal.
        $this->wpService->wpEnqueueStyle(self::STYLE_HANDLE);
        $this->wpService->wpAddInlineStyle(self::STYLE_HANDLE, $styles);
    }
}
