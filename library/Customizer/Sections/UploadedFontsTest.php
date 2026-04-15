<?php

declare(strict_types=1);

namespace Municipio\Customizer\Sections;

use Municipio\Customizer\Fonts\FontCatalog;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

/**
 * Tests uploaded fonts section field configuration.
 */
class UploadedFontsTest extends TestCase
{
    #[TestDox('getFieldArgs() uses Add font button and numbered Font row labels')]
    public function testGetFieldArgsUsesAddFontButtonAndNumberedFontRowLabels(): void
    {
        $args = UploadedFonts::getFieldArgs('municipio_customizer_section_uploaded_fonts');

        static::assertSame('repeater', $args['type']);
        static::assertSame(FontCatalog::UPLOADED_FONTS_SETTING, $args['settings']);
        static::assertSame('municipio_customizer_section_uploaded_fonts', $args['section']);
        static::assertSame('Add font', $args['button_label']);
        static::assertSame('field', $args['row_label']['type']);
        static::assertSame('Font', $args['row_label']['value']);
        static::assertSame('name', $args['row_label']['field']);
    }
}
