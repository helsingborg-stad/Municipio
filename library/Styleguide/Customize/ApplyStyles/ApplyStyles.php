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

    public function __construct(
        private AddAction&AddFilter&WpRegisterStyle&WpEnqueueStyle&WpAddInlineStyle&GetThemeMod $wpService,
    ) {}

    public function addHooks(): void
    {
        $this->wpService->addAction('wp_enqueue_scripts', [$this, 'applyStyles']);
        $this->wpService->addAction('enqueue_block_editor_assets', [$this, 'applyStyles']);
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
        $css = (new DesignTokensToCssConverter\DesignTokensToCssConverter())->convert($tokens);

        $styles = '@layer theme {' . $css . $this->getEditorRootTokensAsCss($storedTokens['token']) . '}';

        return $styles;
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

        $rows = [self::EDITOR_ROOT_SELECTOR . ' {'];

        foreach ($rootTokens as $token => $value) {
            $rows[] = sprintf('%s: %s;', $token, (string) $value);
        }

        $rows[] = '}';

        return implode("\n", $rows);
    }

    private function getStoredTokens(): array
    {
        $stored = $this->wpService->getThemeMod('tokens');
        return $this->sanitizeStoredValue($stored);
    }

    private function sanitizeStoredValue(mixed $value): array
    {
        $default = ['token' => [], 'component' => []];

        if (!is_string($value)) {
            return $default;
        }

        $decoded = json_decode($value, true);

        if (!is_array($decoded) || !isset($decoded['token']) || !isset($decoded['component'])) {
            return $default;
        }

        return $decoded;
    }

    private function applyInlineStyles(string $styles): void
    {
        $this->wpService->wpRegisterStyle(self::STYLE_HANDLE, false); // Not applying source since inline styles are the only goal.
        $this->wpService->wpEnqueueStyle(self::STYLE_HANDLE);
        $this->wpService->wpAddInlineStyle(self::STYLE_HANDLE, $styles);
    }
}
