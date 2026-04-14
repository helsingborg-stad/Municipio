<?php

declare(strict_types=1);

namespace Municipio\Customizer\Fonts;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

/**
 * Tests Google Fonts URL generation.
 */
class GoogleFontsUrlBuilderTest extends TestCase
{
    #[TestDox('build() creates a CSS2 URL with weight and italic axes')]
    public function testBuildCreatesCss2UrlWithWeightAndItalicAxes(): void
    {
        $builder = new GoogleFontsUrlBuilder();

        $url = $builder->build(
            ['Roboto', 'Open Sans'],
            [
                'Roboto' => [
                    'variants' => ['regular', '500', '700italic'],
                ],
                'Open Sans' => [
                    'variants' => ['300', 'italic'],
                ],
            ],
        );

        static::assertSame(
            'https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,400;0,500;1,700&family=Open+Sans:ital,wght@0,300;1,400&display=swap',
            $url,
        );
    }
}
