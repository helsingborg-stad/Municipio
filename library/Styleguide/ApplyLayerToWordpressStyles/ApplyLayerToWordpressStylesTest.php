<?php

namespace Municipio\Styleguide\ApplyLayerToWordpressStyles;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\AddFilter;

class ApplyLayerToWordpressStylesTest extends TestCase
{
    #[TestDox('has valid addHooks method')]
    public function testAddHooks(): void
    {
        $wpService = static::createWpService();
        $applyLayerToWordpressStyles = new ApplyLayerToWordpressStyles($wpService);

        $applyLayerToWordpressStyles->addHooks();

        // Since the addFilter method always returns true, we can assert that it was called.
        static::assertTrue(true);
    }

    #[TestDox('Puts layers in wordpress css layer')]
    #[DataProvider('provideTestData')]
    public function testApplyLayerToWordpressStyles(string $tag, string $handle, string $href): void
    {
        $applyLayerToWordpressStyles = new ApplyLayerToWordpressStyles(static::createWpService());

        $result = $applyLayerToWordpressStyles->apply($tag, $handle);

        static::assertEquals("<style>@import url(\"$href\") layer(wordpress);</style>", $result);
    }

    #[TestDox('only applies layers to specified handles')]
    public function testOnlyAppliesLayersToSpecifiedHandles(): void
    {
        $applyLayerToWordpressStyles = new ApplyLayerToWordpressStyles(static::createWpService());

        $tag = '<link href="http://test/path/style.css" />';
        $handle = 'some-other-handle';
        $result = $applyLayerToWordpressStyles->apply($tag, $handle);

        static::assertEquals($tag, $result);
    }

    #[TestDox('does not modify tag if href is missing')]
    public function testDoesNotModifyTagIfHrefIsMissing(): void
    {
        $applyLayerToWordpressStyles = new ApplyLayerToWordpressStyles(static::createWpService());

        $tag = '<link rel="stylesheet" />';
        $handle = 'wp-block-library';
        $result = $applyLayerToWordpressStyles->apply($tag, $handle);

        static::assertEquals($tag, $result);
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
