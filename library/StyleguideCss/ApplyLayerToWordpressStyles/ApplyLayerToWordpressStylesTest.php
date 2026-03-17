<?php

namespace Municipio\StyleguideCss\ApplyLayerToWordpressStyles;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\AddFilter;

class ApplyLayerToWordpressStylesTest extends TestCase
{
    #[TestDox('Puts layers in wordpress css layer')]
    #[DataProvider('provideTestData')]
    public function testApplyLayerToWordpressStyles(string $tag, string $handle, string $href): void
    {
        $applyLayerToWordpressStyles = new ApplyLayerToWordpressStyles(static::createWpService());

        $result = $applyLayerToWordpressStyles->apply($tag, $handle);

        static::assertEquals("<style>@import url(\"$href\") layer(wordpress);</style>", $result);
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
