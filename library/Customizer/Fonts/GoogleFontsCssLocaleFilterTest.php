<?php

declare(strict_types=1);

namespace Municipio\Customizer\Fonts;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

/**
 * Tests locale-based filtering of Google Fonts inline CSS.
 */
class GoogleFontsCssLocaleFilterTest extends TestCase
{
    #[TestDox('addHooks() registers kirki inline font filtering')]
    public function testAddHooksRegistersKirkiInlineFontFiltering(): void
    {
        $wpService = new FakeWpService([
            'addFilter' => true,
        ]);

        $filter = new GoogleFontsCssLocaleFilter($wpService);
        $filter->addHooks();

        static::assertSame('kirki_inline_fonts', $wpService->methodCalls['addFilter'][0][0]);
    }

    #[TestDox('filterCssByLocale() keeps latin and latin-ext blocks for Swedish locale')]
    public function testFilterCssByLocaleKeepsLatinAndLatinExtBlocksForSwedishLocale(): void
    {
        $wpService = new FakeWpService([
            'getLocale' => 'sv_SE',
        ]);

        $filter = new GoogleFontsCssLocaleFilter($wpService);

        $css = '/* cyrillic */@font-face{font-family:"Roboto";unicode-range:U+0400-04FF;}'
            . '/* latin-ext */@font-face{font-family:"Roboto";unicode-range:U+0100-024F;}'
            . '/* latin */@font-face{font-family:"Roboto";unicode-range:U+0000-00FF;}';

        $result = $filter->filterCssByLocale($css);

        static::assertStringNotContainsString('/* cyrillic */', $result);
        static::assertStringContainsString('/* latin-ext */', $result);
        static::assertStringContainsString('/* latin */', $result);
    }

    #[TestDox('filterCssByLocale() keeps only latin block for English locale')]
    public function testFilterCssByLocaleKeepsOnlyLatinBlockForEnglishLocale(): void
    {
        $wpService = new FakeWpService([
            'getLocale' => 'en_US',
        ]);

        $filter = new GoogleFontsCssLocaleFilter($wpService);

        $css = '/* latin-ext */@font-face{font-family:"Roboto";unicode-range:U+0100-024F;}'
            . '/* latin */@font-face{font-family:"Roboto";unicode-range:U+0000-00FF;}';

        $result = $filter->filterCssByLocale($css);

        static::assertStringNotContainsString('/* latin-ext */', $result);
        static::assertStringContainsString('/* latin */', $result);
    }

    #[TestDox('filterCssByLocale() keeps cyrillic blocks for Russian locale')]
    public function testFilterCssByLocaleKeepsCyrillicBlocksForRussianLocale(): void
    {
        $wpService = new FakeWpService([
            'getLocale' => 'ru_RU',
        ]);

        $filter = new GoogleFontsCssLocaleFilter($wpService);

        $css = '/* latin */@font-face{font-family:"Roboto";unicode-range:U+0000-00FF;}'
            . '/* cyrillic */@font-face{font-family:"Roboto";unicode-range:U+0400-04FF;}'
            . '/* cyrillic-ext */@font-face{font-family:"Roboto";unicode-range:U+0460-052F;}';

        $result = $filter->filterCssByLocale($css);

        static::assertStringNotContainsString('/* latin */', $result);
        static::assertStringContainsString('/* cyrillic */', $result);
        static::assertStringContainsString('/* cyrillic-ext */', $result);
    }

    #[TestDox('filterCssByLocale() leaves CSS untouched when no subset blocks can be detected')]
    public function testFilterCssByLocaleLeavesCssUntouchedWhenNoSubsetBlocksCanBeDetected(): void
    {
        $wpService = new FakeWpService([
            'getLocale' => 'sv_SE',
        ]);

        $filter = new GoogleFontsCssLocaleFilter($wpService);

        $css = '@font-face{font-family:"Roboto";src:url("https://fonts.gstatic.com/test.woff2") format("woff2");}';

        $result = $filter->filterCssByLocale($css);

        static::assertSame($css, $result);
    }

    #[TestDox('filterCssByLocale() infers subsets from unicode ranges when subset comments are missing')]
    public function testFilterCssByLocaleInfersSubsetsFromUnicodeRangesWhenSubsetCommentsAreMissing(): void
    {
        $wpService = new FakeWpService([
            'getLocale' => 'sv_SE',
        ]);

        $filter = new GoogleFontsCssLocaleFilter($wpService);

        $css = '@font-face{font-family:"Arimo";unicode-range:U+0400-045F;}'
            . '@font-face{font-family:"Arimo";unicode-range:U+0100-02BA, U+1E00-1E9F;}'
            . '@font-face{font-family:"Arimo";unicode-range:U+0000-00FF, U+20AC;}';

        $result = $filter->filterCssByLocale($css);

        static::assertStringNotContainsString('U+0400-045F', $result);
        static::assertStringContainsString('U+0100-02BA', $result);
        static::assertStringContainsString('U+0000-00FF', $result);
    }

    #[TestDox('filterCssByLocale() infers latin only for English locale when subset comments are missing')]
    public function testFilterCssByLocaleInfersLatinOnlyForEnglishLocaleWhenSubsetCommentsAreMissing(): void
    {
        $wpService = new FakeWpService([
            'getLocale' => 'en_US',
        ]);

        $filter = new GoogleFontsCssLocaleFilter($wpService);

        $css = '@font-face{font-family:"Arimo";unicode-range:U+0100-02BA, U+1E00-1E9F;}'
            . '@font-face{font-family:"Arimo";unicode-range:U+0000-00FF, U+20AC;}';

        $result = $filter->filterCssByLocale($css);

        static::assertStringNotContainsString('U+0100-02BA', $result);
        static::assertStringContainsString('U+0000-00FF', $result);
    }
}
