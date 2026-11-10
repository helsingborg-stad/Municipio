<?php

declare(strict_types=1);


namespace Municipio\Styleguide\Customize\ApplyStyles;

use Municipio\HooksRegistrar\Hookable;
use Municipio\Styleguide\Customize\TokenData\TokenData;
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

    /** @var list<array<string, mixed>>|null */
    private ?array $styleguideColorSettings = null;

    /** @var array<string, string>|null */
    private ?array $editorComponentColorTokens = null;

    public function __construct(
        private AddAction&AddFilter&WpRegisterStyle&WpEnqueueStyle&WpAddInlineStyle&GetThemeMod $wpService,
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
        $styles = $this->getTokensAsCss();

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

    private function getTokensAsCss(): string
    {
        $storedTokens = $this->getStoredTokens();
        $tokens = array_merge($storedTokens['token'], $storedTokens['component']);
        $converter = new DesignTokensToCssConverter\DesignTokensToCssConverter();
        $css = $converter->convert($tokens);

        $styles = '@layer theme {'
            . $css
            . $this->getEditorRootTokensAsCss($storedTokens['token'])
            . $this->getColorSchemeStyles($converter, $storedTokens['_dark'], self::DARK_SCHEME_QUERY)
            // High contrast deliberately follows dark mode when both preferences match.
            . $this->getColorSchemeStyles($converter, $storedTokens['_contrast'], self::CONTRAST_SCHEME_QUERY)
            . '}';

        return $styles;
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
    ): string {
        $tokens = ColorSchemeOverrides::filter($tokens);

        if ($tokens === []) {
            return '';
        }

        $rootStyles = $converter->convertRootTokens($tokens);
        $editorStyles = $this->getEditorRootTokensAsCss($tokens);

        return sprintf('@media %s {%s%s}', $mediaQuery, $rootStyles, $editorStyles);
    }

    /**
     * Mirrors root tokens onto the editor wrapper so stored theme tokens win over
     * editor defaults that are defined on the same ancestor element.
     *
     * @param array<string, mixed> $rootTokens
     */
    private function getEditorRootTokensAsCss(array $rootTokens): string
    {
        $rootTokens = array_filter(
            $rootTokens,
            static fn ($key): bool => is_string($key) && str_starts_with($key, '--'),
            ARRAY_FILTER_USE_KEY,
        );

        if (empty($rootTokens)) {
            return '';
        }

        /*
         * Derived colour tokens are normally declared on :root by the
         * Styleguide. CSS custom properties inherit their computed value, so
         * a derived value inherited by the editor would otherwise still hold
         * the light-scheme calculation. Re-declaring the formulas on the
         * editor wrapper makes them resolve from that wrapper's scheme values.
         */
        $editorTokens = array_merge(
            $this->getDerivedStyleguideColorTokens(),
            $rootTokens,
            $this->getEditorComponentColorTokens(),
        );

        $rows = [self::EDITOR_ROOT_SELECTOR . ' {'];

        foreach ($editorTokens as $token => $value) {
            $rows[] = sprintf('%s: %s;', $token, (string) $value);
        }

        foreach ($this->getUtilityColorAliases($editorTokens) as $alias => $value) {
            $rows[] = sprintf('%s: %s;', $alias, $value);
        }

        $rows[] = '}';

        return implode("\n", $rows);
    }

    /**
     * Mirrors Styleguide colour utility aliases on the editor wrapper.
     *
     * Utility CSS declares these aliases on :root. In the editor, stored
     * palette and colour-scheme tokens are scoped to .editor-styles-wrapper,
     * so aliases must be scoped there too for their var() references to pick
     * up dark and high-contrast values.
     *
     * @param array<string, mixed> $tokens
     * @return array<string, string>
     */
    private function getUtilityColorAliases(array $tokens): array
    {
        $aliases = [];

        foreach (array_unique(array_merge(array_keys($tokens), $this->getStyleguideColorTokenVariables())) as $token) {
            if (!is_string($token) || !str_starts_with($token, '--color--')) {
                continue;
            }

            $suffix = substr($token, strlen('--color--'));
            $aliases['--u-color--color-' . $suffix] = 'var(' . $token . ')';
        }

        return $aliases;
    }

    /**
     * Gets the Styleguide's calculated colour declarations. These are kept in
     * the Styleguide data source rather than duplicated in Municipio, which
     * means new palette roles are automatically reflected in the editor.
     *
     * @return array<string, string>
     */
    private function getDerivedStyleguideColorTokens(): array
    {
        $tokens = [];

        foreach ($this->getStyleguideColorSettings() as $setting) {
            $variable = $setting['variable'] ?? null;
            $default = $setting['default'] ?? null;

            if (!is_string($variable) || !is_scalar($default) || !str_contains((string) $default, 'var(')) {
                continue;
            }

            $tokens[$variable] = (string) $default;
        }

        return $tokens;
    }

    /**
     * @return list<string>
     */
    private function getStyleguideColorTokenVariables(): array
    {
        return array_values(array_filter(
            array_map(
                static fn (array $setting): ?string => is_string($setting['variable'] ?? null) ? $setting['variable'] : null,
                $this->getStyleguideColorSettings(),
            ),
        ));
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function getStyleguideColorSettings(): array
    {
        if ($this->styleguideColorSettings !== null) {
            return $this->styleguideColorSettings;
        }

        $contents = file_get_contents(TokenData::getFilePath());
        $tokenData = $contents === false ? null : json_decode($contents, true);

        if (!is_array($tokenData) || !is_array($tokenData['categories'] ?? null)) {
            return $this->styleguideColorSettings = [];
        }

        $settings = [];

        foreach ($tokenData['categories'] as $category) {
            if (!is_array($category) || !is_array($category['settings'] ?? null)) {
                continue;
            }

            foreach ($category['settings'] as $setting) {
                if (is_array($setting) && str_starts_with((string) ($setting['variable'] ?? ''), '--color--')) {
                    $settings[] = $setting;
                }
            }
        }

        return $this->styleguideColorSettings = $settings;
    }

    /**
     * Mirrors component colour defaults that the Styleguide declares on
     * :root. A property such as --c-card--color--surface-default resolves its
     * var(--color--surface) reference at :root before it is inherited, which
     * leaves it stuck on the light value in the editor. Declaring it on the
     * editor wrapper lets it resolve from the wrapper's active colour scheme.
     *
     * The declarations are extracted from built component CSS so this stays
     * in lockstep with the installed Styleguide rather than maintaining a
     * hand-written list of component tokens.
     *
     * @return array<string, string>
     */
    private function getEditorComponentColorTokens(): array
    {
        if ($this->editorComponentColorTokens !== null) {
            return $this->editorComponentColorTokens;
        }

        $styleguideDirectory = dirname(__DIR__, 4) . '/assets/dist/styleguide';
        $manifestPath = $styleguideDirectory . '/manifest.json';
        $contents = file_get_contents($manifestPath);
        $manifest = $contents === false ? null : json_decode($contents, true);

        if (!is_array($manifest)) {
            return $this->editorComponentColorTokens = [];
        }

        $tokens = [];

        foreach ($manifest as $source => $built) {
            if (!is_string($source) || !is_string($built) || !str_starts_with($source, 'css/components/')) {
                continue;
            }

            $path = realpath($styleguideDirectory . '/' . $built);
            if ($path === false || !str_starts_with($path, $styleguideDirectory . '/') || !is_file($path)) {
                continue;
            }

            $css = file_get_contents($path);
            if ($css === false) {
                continue;
            }

            preg_match_all('/:root\\s*\\{([^{}]*)\\}/', $css, $rootBlocks);

            foreach ($rootBlocks[1] ?? [] as $declarations) {
                preg_match_all('/(--[a-zA-Z][a-zA-Z0-9-]*)\\s*:\\s*([^;{}]+);/', $declarations, $matches, PREG_SET_ORDER);

                foreach ($matches as $match) {
                    if (!str_contains($match[2], 'var(--color--')) {
                        continue;
                    }

                    $tokens[$match[1]] = trim($match[2]);
                }
            }
        }

        return $this->editorComponentColorTokens = $tokens;
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

        if (!is_array($decoded) || !isset($decoded['token']) || !isset($decoded['component']) || !is_array($decoded['token']) || !is_array($decoded['component'])) {
            return $default;
        }

        return [
            'token' => ColorSchemeOverrides::filterSafeRootTokens($decoded['token']),
            '_dark' => ColorSchemeOverrides::filter(is_array($decoded['_dark'] ?? null) ? $decoded['_dark'] : null),
            '_contrast' => ColorSchemeOverrides::filter(is_array($decoded['_contrast'] ?? null) ? $decoded['_contrast'] : null),
            'component' => $decoded['component'],
        ];
    }

    private function applyInlineStyles(string $styles): void
    {
        $this->wpService->wpRegisterStyle(self::STYLE_HANDLE, false); // Not applying source since inline styles are the only goal.
        $this->wpService->wpEnqueueStyle(self::STYLE_HANDLE);
        $this->wpService->wpAddInlineStyle(self::STYLE_HANDLE, $styles);
    }
}
