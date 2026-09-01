<?php

declare(strict_types=1);

namespace Municipio\Styleguide\ApplyLayersToEnqueuedStyles;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\AddFilter;
use WpService\Contracts\GetCurrentScreen;
use WpService\Contracts\IsAdmin;

class ApplyLayersToEnqueuedStylesTest extends TestCase
{
    #[TestDox('has valid addHooks method')]
    public function testAddHooks(): void
    {
        $wpService = static::createWpService();
        $ApplyLayersToEnqueuedStyles = new ApplyLayersToEnqueuedStyles($wpService);

        $ApplyLayersToEnqueuedStyles->addHooks();

        // Since the addFilter method always returns true, we can assert that it was called.
        static::assertTrue(true);
    }

    #[TestDox('Puts layers in wordpress css layer')]
    #[DataProvider('provideTestData')]
    public function testApplyLayersToEnqueuedStyles(string $tag, string $handle, string $href): void
    {
        $ApplyLayersToEnqueuedStyles = new ApplyLayersToEnqueuedStyles($this->createWpService());

        $result = $ApplyLayersToEnqueuedStyles->apply($tag, $handle, $href);

        static::assertSame("<style>@import url(\"{$href}\") layer(wordpress);</style>", $result);
    }

    #[TestDox('only applies layers to specified handles')]
    public function testOnlyAppliesLayersToSpecifiedHandles(): void
    {
        $ApplyLayersToEnqueuedStyles = new ApplyLayersToEnqueuedStyles($this->createWpService());

        $href = 'http://test/path/style.css';
        $tag = "<link href=\"{$href}\" />";
        $handle = 'some-other-handle';
        $result = $ApplyLayersToEnqueuedStyles->apply($tag, $handle, $href);

        static::assertEquals($tag, $result);
    }

    #[TestDox('does not modify tag if href is missing')]
    public function testDoesNotModifyTagIfHrefIsMissing(): void
    {
        $ApplyLayersToEnqueuedStyles = new ApplyLayersToEnqueuedStyles($this->createWpService());

        $href = '';
        $tag = '<link rel="stylesheet" />';
        $handle = 'wp-block-library';
        $result = $ApplyLayersToEnqueuedStyles->apply($tag, $handle, $href);

        static::assertEquals($tag, $result);
    }

    #[TestDox('uses the correct layer based on handle')]
    public function testUsesCorrectLayerBasedOnHandle(): void
    {
        $handle = 'admin-bar';
        $href = 'http://test/path/style.css';
        $tag = "<link href=\"{$href}\" />";
        $ApplyLayersToEnqueuedStyles = new ApplyLayersToEnqueuedStyles(static::createWpService());

        $result = $ApplyLayersToEnqueuedStyles->apply($tag, $handle, $href);

        static::assertSame('<style>@import url("http://test/path/style.css") layer(theme);</style>', $result);
    }

    #[TestDox('preserves id when converting stylesheet link to layered style tag')]
    public function testPreservesIdWhenConvertingStylesheetLinkToLayeredStyleTag(): void
    {
        $handle = 'admin-bar';
        $href = 'http://test/path/style.css';
        $tag = "<link id=\"wp-custom-css\" href=\"{$href}\" />";
        $ApplyLayersToEnqueuedStyles = new ApplyLayersToEnqueuedStyles(static::createWpService());

        $result = $ApplyLayersToEnqueuedStyles->apply($tag, $handle, $href);

        static::assertSame('<style id="wp-custom-css">@import url("http://test/path/style.css") layer(theme);</style>', $result);
    }

    public static function provideTestData(): array
    {
        return [
            'wp-block-library' => [
                '<link href="http://test/path/wp-includes/style.css" />',
                'some-wordpress-handle',
                'http://test/path/wp-includes/style.css',
            ],
        ];
    }

    private function createWpService(): AddFilter&IsAdmin&GetCurrentScreen
    {
        return new class implements AddFilter, IsAdmin, GetCurrentScreen {
            public function addFilter(string $hookName, callable $callback, int $priority = 10, int $acceptedArgs = 1): true
            {
                return true;
            }

            public function isAdmin(): bool
            {
                return false;
            }

            public function getCurrentScreen(): ?\WP_Screen
            {
                return null;
            }
        };
    }
}
