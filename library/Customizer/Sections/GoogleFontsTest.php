<?php

declare(strict_types=1);

namespace Municipio\Customizer\Sections;

use Municipio\Customizer\Fonts\FontCatalog;
use Municipio\Customizer\Fonts\Sections\GoogleFonts as GoogleFontsSection;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

/**
 * Tests Google Fonts section field configuration.
 */
class GoogleFontsTest extends TestCase
{
    #[TestDox('getFieldArgs() uses searchable multi-select control for enabled Google fonts')]
    public function testGetFieldArgsUsesSearchableMultiSelectControlForEnabledGoogleFonts(): void
    {
        $args = GoogleFontsSection::getFieldArgs('municipio_customizer_section_google_fonts');

        static::assertSame('select', $args['type']);
        static::assertSame(FontCatalog::GOOGLE_FONTS_SETTING, $args['settings']);
        static::assertSame('municipio_customizer_section_google_fonts', $args['section']);
        static::assertSame(999, $args['multiple']);
        static::assertTrue($args['clearable']);
        static::assertSame('Search fonts...', $args['placeholder']);
        static::assertIsArray($args['choices']);
        static::assertIsArray($args['default']);
    }
}
