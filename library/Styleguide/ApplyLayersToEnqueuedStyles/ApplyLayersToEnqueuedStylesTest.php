<?php

namespace Municipio\Styleguide\ApplyLayersToEnqueuedStyles;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\AddFilter;

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
        $ApplyLayersToEnqueuedStyles = new ApplyLayersToEnqueuedStyles(static::createWpService());

        $result = $ApplyLayersToEnqueuedStyles->apply($tag, $handle);

        static::assertEquals("<style>@import url(\"$href\") layer(wordpress);</style>", $result);
    }

    #[TestDox('only applies layers to specified handles')]
    public function testOnlyAppliesLayersToSpecifiedHandles(): void
    {
        $ApplyLayersToEnqueuedStyles = new ApplyLayersToEnqueuedStyles(static::createWpService());

        $tag = '<link href="http://test/path/style.css" />';
        $handle = 'some-other-handle';
        $result = $ApplyLayersToEnqueuedStyles->apply($tag, $handle);

        static::assertEquals($tag, $result);
    }

    #[TestDox('does not modify tag if href is missing')]
    public function testDoesNotModifyTagIfHrefIsMissing(): void
    {
        $ApplyLayersToEnqueuedStyles = new ApplyLayersToEnqueuedStyles(static::createWpService());

        $tag = '<link rel="stylesheet" />';
        $handle = 'wp-block-library';
        $result = $ApplyLayersToEnqueuedStyles->apply($tag, $handle);

        static::assertEquals($tag, $result);
    }

    #[TestDox('uses the correct layer based on handle')]
    public function testUsesCorrectLayerBasedOnHandle(): void
    {
        $handle = 'css-municipiocss';
        $tag = '<link href="http://test/path/style.css" />';
        $ApplyLayersToEnqueuedStyles = new ApplyLayersToEnqueuedStyles(static::createWpService());

        $result = $ApplyLayersToEnqueuedStyles->apply($tag, $handle);

        static::assertEquals('<style>@import url("http://test/path/style.css") layer(theme);</style>', $result);
    }

    public static function provideTestData(): array
    {
        return [
            'wp-block-library' => [
                '<link href="http://test/path/style.css" />',
                'wp-block-library',
                'http://test/path/style.css',
            ],
        ];
    }

    private static function createWpService(): AddFilter
    {
        return new class implements AddFilter {
            public function addFilter(string $hookName, callable $callback, int $priority = 10, int $acceptedArgs = 1): true
            {
                return true;
            }
        };
    }
}
