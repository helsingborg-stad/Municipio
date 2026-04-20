<?php

namespace Municipio\Styleguide\Customize\ApplyStyles;

use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\AddAction;
use WpService\Contracts\GetThemeMod;
use WpService\Contracts\WpAddInlineStyle;
use WpService\Contracts\WpEnqueueStyle;
use WpService\Contracts\WpRegisterStyle;

class ApplyStyles implements Hookable
{
    private const STYLE_HANDLE = 'styleguide-design-builder-output';

    public function __construct(
        private AddAction&WpRegisterStyle&WpEnqueueStyle&WpAddInlineStyle&GetThemeMod $wpService,
    ) {}

    public function addHooks(): void
    {
        $this->wpService->addAction('wp_enqueue_scripts', [$this, 'applyStyles']);
    }

    public function applyStyles(): void
    {
        $styles = $this->getTokensAsCss();
        $this->applyInlineStyles($styles);
    }

    private function getTokensAsCss(): string
    {
        $storedTokens = $this->getStoredTokens();
        $css = (new DesignTokensToCssConverter\DesignTokensToCssConverter())->convert(array_merge($storedTokens['token'], $storedTokens['component']));
        return '@layer theme {' . $css . '}';
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
