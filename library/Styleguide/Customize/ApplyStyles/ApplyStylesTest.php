<?php

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
        static::assertStringContainsString('.c-card {', $wpService->inlineStyles);
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