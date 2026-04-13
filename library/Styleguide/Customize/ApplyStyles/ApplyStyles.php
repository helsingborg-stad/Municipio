<?php

namespace Municipio\Styleguide\Customize\ApplyStyles;

use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\AddAction;
use WpService\Contracts\WpAddInlineStyle;
use WpService\Contracts\WpEnqueueStyle;
use WpService\Contracts\WpRegisterStyle;

class ApplyStyles implements Hookable
{
    private const STYLE_HANDLE = 'styleguide-design-builder-output';

    public function __construct(
        private AddAction&WpRegisterStyle&WpEnqueueStyle&WpAddInlineStyle $wpService,
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
        $stored = get_theme_mod('tokens', json_encode(['token' => [], 'component' => []]));
        $stored = json_decode($stored, true);
        $css = (new DesignTokensToCssConverter\DesignTokensToCssConverter())->convert(array_merge($stored['token'], $stored['component']));
        return '@layer theme {' . $css . '}';
    }

    private function applyInlineStyles(string $styles): void
    {
        $this->wpService->wpRegisterStyle(self::STYLE_HANDLE, false); // Not applying source since inline styles are the only goal.
        $this->wpService->wpEnqueueStyle(self::STYLE_HANDLE);
        $this->wpService->wpAddInlineStyle(self::STYLE_HANDLE, $styles);
    }
}
