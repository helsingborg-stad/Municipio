<?php

declare(strict_types=1);

namespace Municipio\Styleguide\Customize\ApplyStyles;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\AddAction;
use WpService\Contracts\AddFilter;
use WpService\Contracts\GetThemeMod;
use WpService\Contracts\WpAddInlineStyle;
use WpService\Contracts\WpEnqueueStyle;
use WpService\Contracts\WpRegisterStyle;

class ApplyStylesTest extends TestCase
{
    #[TestDox('registers the block editor settings filter for iframe styles')]
    public function testRegistersBlockEditorIframeStylesFilter(): void
    {
        $wpService = $this->createWpService(json_encode([
            'token' => [],
            'component' => [],
        ], JSON_THROW_ON_ERROR));

        $subject = new ApplyStyles($wpService);

        $subject->addHooks();

        static::assertContains('block_editor_settings_all', $wpService->addedFilters);
    }

    #[TestDox('outputs stored root tokens for both root and editor wrapper scopes')]
    public function testOutputsStoredRootTokensForBothRootAndEditorWrapperScopes(): void
    {
        $wpService = $this->createWpService(json_encode([
            'token' => [
                '--border-radius' => '0.25',
                '--color--primary' => '#667b50',
            ],
            'component' => [
                '__general__' => [
                    'card' => [
                        '--c-card--border-width' => '2',
                    ],
                ],
            ],
        ], JSON_THROW_ON_ERROR));

        $subject = new ApplyStyles($wpService);

        $subject->applyStyles();

        static::assertSame('styleguide-design-builder-output', $wpService->registeredHandle);
        static::assertSame('styleguide-design-builder-output', $wpService->enqueuedHandle);
        static::assertNotNull($wpService->inlineStyles);
        static::assertStringContainsString('@layer theme {', $wpService->inlineStyles);
        static::assertStringContainsString(':root {', $wpService->inlineStyles);
        static::assertStringContainsString(':root :where(.editor-styles-wrapper) {', $wpService->inlineStyles);
        static::assertSame(2, substr_count($wpService->inlineStyles, '--border-radius: 0.25;'));
        static::assertStringContainsString('--u-color--color-primary: var(--color--primary);', $wpService->inlineStyles);
        static::assertStringContainsString('--u-color--color-surface: var(--color--surface);', $wpService->inlineStyles);
        static::assertStringContainsString('--color--surface-alt: color-mix(', $wpService->inlineStyles);
        static::assertStringContainsString('--c-card--color--surface-default: var(--color--surface);', $wpService->inlineStyles);
        static::assertStringContainsString('.c-card {', $wpService->inlineStyles);
    }

    #[TestDox('pairs a selected header surface with its semantic contrast at render time')]
    public function testPairsHeaderSurfaceWithSemanticContrastAtRenderTime(): void
    {
        $wpService = $this->createWpService(json_encode([
            'token' => [],
            'component' => [
                'scope:s-header-flexible-upper' => [
                    'header' => [
                        '--c-header--color--surface' => 'var(--color--palette-4)',
                    ],
                ],
            ],
        ], JSON_THROW_ON_ERROR));

        (new ApplyStyles($wpService))->applyStyles();

        static::assertNotNull($wpService->inlineStyles);
        static::assertStringContainsString('--c-header--color--surface: var(--color--palette-4);', $wpService->inlineStyles);
        static::assertStringContainsString('--c-header--color--surface-contrast: var(--color--palette-4-contrast);', $wpService->inlineStyles);
    }

    #[TestDox('does not emit legacy literal header colour overrides')]
    public function testDoesNotEmitLegacyLiteralHeaderColorOverrides(): void
    {
        $wpService = $this->createWpService(json_encode([
            'token' => [],
            'component' => [
                '__general__' => [
                    'header' => [
                        '--c-header--background-color' => '#d9a0a0',
                        '--c-header--color' => '#000000',
                        '--c-header--border-color' => '#000000',
                    ],
                ],
            ],
        ], JSON_THROW_ON_ERROR));

        (new ApplyStyles($wpService))->applyStyles();

        static::assertNotNull($wpService->inlineStyles);
        static::assertStringNotContainsString('--c-header--background-color: #d9a0a0;', $wpService->inlineStyles);
        static::assertStringNotContainsString('--c-header--color: #000000;', $wpService->inlineStyles);
        static::assertStringNotContainsString('--c-header--border-color: #000000;', $wpService->inlineStyles);
    }

    #[TestDox('pairs a footer surface and removes legacy literal footer colours')]
    public function testPairsFooterSurfaceAndRemovesLegacyLiteralFooterColors(): void
    {
        $wpService = $this->createWpService(json_encode([
            'token' => [],
            'component' => [
                '__general__' => [
                    'footer' => [
                        '--c-footer--color--surface' => 'var(--color--palette-2)',
                        '--c-footer--color--surface-contrast' => '#000000',
                        '--c-footer--background-color' => '#d9a0a0',
                        '--c-footer--color' => '#000000',
                        '--c-footer--subfooter-color' => '#000000',
                    ],
                ],
            ],
        ], JSON_THROW_ON_ERROR));

        (new ApplyStyles($wpService))->applyStyles();

        static::assertNotNull($wpService->inlineStyles);
        static::assertStringContainsString('--c-footer--color--surface-contrast: var(--color--palette-2-contrast);', $wpService->inlineStyles);
        static::assertStringNotContainsString('--c-footer--color--surface-contrast: #000000;', $wpService->inlineStyles);
        static::assertStringNotContainsString('--c-footer--background-color: #d9a0a0;', $wpService->inlineStyles);
        static::assertStringNotContainsString('--c-footer--color: #000000;', $wpService->inlineStyles);
        static::assertStringNotContainsString('--c-footer--subfooter-color: #000000;', $wpService->inlineStyles);
    }

    #[TestDox('appends stored tokens to block editor iframe settings styles')]
    public function testAppendsStoredTokensToBlockEditorIframeSettingsStyles(): void
    {
        $wpService = $this->createWpService(json_encode([
            'token' => [
                '--color--primary' => '#667b50',
            ],
            'component' => [],
        ], JSON_THROW_ON_ERROR));

        $subject = new ApplyStyles($wpService);

        $settings = $subject->applyEditorIframeStyles([
            'styles' => [
                ['css' => '.existing { color: red; }'],
            ],
        ]);

        static::assertCount(2, $settings['styles']);
        static::assertSame('.existing { color: red; }', $settings['styles'][0]['css']);
        static::assertStringContainsString('@layer theme {', $settings['styles'][1]['css']);
        static::assertStringContainsString('--color--primary: #667b50;', $settings['styles'][1]['css']);
    }

    #[TestDox('emits optional colour schemes after base tokens and ignores derived scheme tokens')]
    public function testEmitsOptionalColourSchemes(): void
    {
        $wpService = $this->createWpService(json_encode([
            'token' => ['--color--background' => '#ffffff'],
            '_dark' => [
                '--color--background' => '#111111',
                '--color--background-border' => '#222222',
            ],
            '_contrast' => ['--color--background' => '#000000'],
            'component' => [],
        ], JSON_THROW_ON_ERROR));

        (new ApplyStyles($wpService))->applyStyles();

        static::assertNotNull($wpService->inlineStyles);
        static::assertStringContainsString('@media (prefers-color-scheme: dark)', $wpService->inlineStyles);
        static::assertStringContainsString('@media (prefers-contrast: more), (forced-colors: active)', $wpService->inlineStyles);
        static::assertStringNotContainsString('--color--background-border: #222222;', $wpService->inlineStyles);
        static::assertStringContainsString('--u-color--color-background: var(--color--background);', $wpService->inlineStyles);
        static::assertLessThan(
            strpos($wpService->inlineStyles, '@media (prefers-contrast: more), (forced-colors: active)'),
            strpos($wpService->inlineStyles, '@media (prefers-color-scheme: dark)'),
        );
    }

    #[TestDox('does not emit media queries for missing or null colour schemes')]
    public function testDoesNotEmitEmptyColourSchemeMediaQueries(): void
    {
        $wpService = $this->createWpService(json_encode([
            'token' => ['--color--background' => '#ffffff'],
            '_dark' => null,
            'component' => [],
        ], JSON_THROW_ON_ERROR));

        (new ApplyStyles($wpService))->applyStyles();

        static::assertNotNull($wpService->inlineStyles);
        static::assertStringNotContainsString('@media', $wpService->inlineStyles);
    }

    #[TestDox('does not emit unsafe base token values')]
    public function testDoesNotEmitUnsafeBaseTokenValues(): void
    {
        $wpService = $this->createWpService(json_encode([
            'token' => [
                '--color--primary' => '#667b50; } </style><script>alert(1)</script>',
                '--border-radius' => '0.25',
            ],
            'component' => [],
        ], JSON_THROW_ON_ERROR));

        (new ApplyStyles($wpService))->applyStyles();

        static::assertNotNull($wpService->inlineStyles);
        static::assertStringContainsString('--border-radius: 0.25;', $wpService->inlineStyles);
        static::assertStringNotContainsString('<script>', $wpService->inlineStyles);
        static::assertStringNotContainsString('#667b50; }', $wpService->inlineStyles);
    }

    private function createWpService(string $storedTokens): AddAction&AddFilter&WpRegisterStyle&WpEnqueueStyle&WpAddInlineStyle&GetThemeMod
    {
        return new class ($storedTokens) implements AddAction, AddFilter, WpRegisterStyle, WpEnqueueStyle, WpAddInlineStyle, GetThemeMod {
            public array $addedFilters = [];
            public ?string $registeredHandle = null;
            public ?string $enqueuedHandle = null;
            public ?string $inlineStyles = null;

            public function __construct(private readonly string $storedTokens)
            {
            }

            public function addAction(string $hookName, callable $callback, int $priority = 10, int $acceptedArgs = 1): true
            {
                return true;
            }

            public function addFilter(string $hookName, callable $callback, int $priority = 10, int $acceptedArgs = 1): true
            {
                $this->addedFilters[] = $hookName;
                return true;
            }

            public function wpRegisterStyle(string $handle, string|false $src, array $deps = [], string|bool|null $ver = false, string $media = 'all'): bool
            {
                $this->registeredHandle = $handle;
                return true;
            }

            public function wpEnqueueStyle(string $handle, string $src = '', array $deps = [], string|bool|null $ver = false, string $media = 'all'): void
            {
                $this->enqueuedHandle = $handle;
            }

            public function wpAddInlineStyle(string $handle, string $data): bool
            {
                $this->inlineStyles = $data;
                return true;
            }

            public function getThemeMod(string $name, mixed $default = false): mixed
            {
                return $name === 'tokens' ? $this->storedTokens : $default;
            }
        };
    }
}
