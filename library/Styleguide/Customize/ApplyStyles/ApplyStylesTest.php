<?php

namespace Municipio\Styleguide\Customize\ApplyStyles;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\AddAction;
use WpService\Contracts\GetThemeMod;
use WpService\Contracts\WpAddInlineStyle;
use WpService\Contracts\WpEnqueueStyle;
use WpService\Contracts\WpRegisterStyle;

class ApplyStylesTest extends TestCase
{
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

    private function createWpService(string $storedTokens): AddAction&WpRegisterStyle&WpEnqueueStyle&WpAddInlineStyle&GetThemeMod
    {
        return new class ($storedTokens) implements AddAction, WpRegisterStyle, WpEnqueueStyle, WpAddInlineStyle, GetThemeMod {
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