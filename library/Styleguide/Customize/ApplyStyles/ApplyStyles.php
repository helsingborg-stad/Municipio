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
        $this->wpService->addFilter('block_editor_settings_all', [$this, 'applyEditorIframeStyles'], 10, 2);
    }

    public function applyStyles(): void
    {
        $styles = $this->getTokensAsCss();
        $this->applyInlineStyles($styles);
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
            . $this->getColorSchemeStyles($converter, $storedTokens['_dark'], self::DARK_SCHEME_QUERY, $editorSelector)
            // High contrast deliberately follows dark mode when both preferences match.
            . $this->getColorSchemeStyles($converter, $storedTokens['_contrast'], self::CONTRAST_SCHEME_QUERY, $editorSelector)
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
    ): string {
        $tokens = ColorSchemeOverrides::filter($tokens);

        if ($tokens === []) {
            return '';
        }

        $rootStyles = $converter->convertRootTokens($tokens);
        $editorStyles = $this->editorColorTokens->getCss($tokens, $editorSelector);

        return sprintf('@media %s {%s%s}', $mediaQuery, $rootStyles, $editorStyles);
    }

    private function getStoredTokens(): array
    {
        $stored = $this->wpService->getThemeMod('tokens');
        return $this->sanitizeStoredValue($stored);
    }

    private function sanitizeStoredValue(mixed $value): array
    {
        $default = ['token' => [], '_dark' => null, '_contrast' => null, 'component' => []];

        if (!is_string($value)) {
            return $default;
        }

        $decoded = json_decode($value, true);

        if (!is_array($decoded) || !is_array($decoded['token'] ?? null) || !is_array($decoded['component'] ?? null)) {
            return $default;
        }

        return [
            'token' => ColorSchemeOverrides::filterSafeRootTokens($decoded['token']),
            '_dark' => ColorSchemeOverrides::filter(is_array($decoded['_dark'] ?? null) ? $decoded['_dark'] : null),
            '_contrast' => ColorSchemeOverrides::filter(is_array($decoded['_contrast'] ?? null) ? $decoded['_contrast'] : null),
            'component' => $this->completeFooterColorPairs(
                $this->completeHeaderColorPairs($decoded['component']),
            ),
        ];
    }

    /**
     * Supplies the paired contrast token for header surfaces selected by older
     * configurations and by Design Center controls that only persist a surface
     * role. This is deliberately applied to the emitted CSS rather than
     * storing a literal colour: palette and colour-scheme changes therefore
     * remain live through the Styleguide variables.
     *
     * @param array<string, mixed> $components
     * @return array<string, mixed>
     */
    private function completeHeaderColorPairs(array $components): array
    {
        foreach ($components as &$componentGroup) {
            if (!is_array($componentGroup) || !is_array($componentGroup['header'] ?? null)) {
                continue;
            }

            $header = &$componentGroup['header'];
            // These were legacy computed colour overrides. Components must
            // consume semantic surface/contrast tokens instead, so a saved
            // literal cannot override the selected colour pair.
            unset(
                $header['--c-header--background-color'],
                $header['--c-header--color'],
                $header['--c-header--border-color'],
            );

            $surface = $header['--c-header--color--surface'] ?? null;
            $contrast = $header['--c-header--color--surface-contrast'] ?? null;

            if ((is_string($contrast) && trim($contrast) !== '') || !is_string($surface)) {
                unset($header);
                continue;
            }

            if (preg_match('/^var\\(--color--(primary|secondary|background|surface|palette-(?:[1-9]|10))\\)$/', trim($surface), $matches) === 1) {
                $header['--c-header--color--surface-contrast'] = 'var(--color--' . $matches[1] . '-contrast)';
            }

            unset($header);
        }
        unset($componentGroup);

        return $components;
    }

    /**
     * Removes legacy computed footer colours and completes the semantic
     * surface/contrast pair used by the Styleguide footer.
     *
     * @param array<string, mixed> $components
     * @return array<string, mixed>
     */
    private function completeFooterColorPairs(array $components): array
    {
        foreach ($components as &$componentGroup) {
            if (!is_array($componentGroup) || !is_array($componentGroup['footer'] ?? null)) {
                continue;
            }

            $footer = &$componentGroup['footer'];
            unset(
                $footer['--c-footer--background-color'],
                $footer['--c-footer--color'],
                $footer['--c-footer--border-color'],
                $footer['--c-footer--subfooter-background-color'],
                $footer['--c-footer--subfooter-color'],
                $footer['--c-footer--subfooter-separator-color'],
            );

            $surface = $footer['--c-footer--color--surface'] ?? null;
            $contrast = $footer['--c-footer--color--surface-contrast'] ?? null;

            if ((!is_string($contrast) || trim($contrast) === '') && is_string($surface)
                && preg_match('/^var\\(--color--(primary|secondary|background|surface|palette-(?:[1-9]|10))\\)$/', trim($surface), $matches) === 1) {
                $footer['--c-footer--color--surface-contrast'] = 'var(--color--' . $matches[1] . '-contrast)';
            }

            unset($footer);
        }
        unset($componentGroup);

        return $components;
    }

    private function applyInlineStyles(string $styles): void
    {
        $this->wpService->wpRegisterStyle(self::STYLE_HANDLE, false); // Not applying source since inline styles are the only goal.
        $this->wpService->wpEnqueueStyle(self::STYLE_HANDLE);
        $this->wpService->wpAddInlineStyle(self::STYLE_HANDLE, $styles);
    }
}
