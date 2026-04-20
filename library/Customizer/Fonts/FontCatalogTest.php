<?php

declare(strict_types=1);

namespace Municipio\Customizer\Fonts;

use Municipio\Customizer\Fonts\Sections\GoogleFonts as GoogleFontsSection;
use Municipio\Customizer\Fonts\Sections\UploadedFonts as UploadedFontsSection;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

/**
 * Tests frontend font declaration output.
 */
class FontCatalogTest extends TestCase
{
    #[TestDox('GoogleFonts section field args use searchable multi-select control')]
    public function testGoogleFontsSectionFieldArgsUseSearchableMultiSelectControl(): void
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

    #[TestDox('UploadedFonts section field args use add font button and file-only rows')]
    public function testUploadedFontsSectionFieldArgsUseAddFontButtonAndFileOnlyRows(): void
    {
        $args = UploadedFontsSection::getFieldArgs('municipio_customizer_section_uploaded_fonts');

        static::assertSame('repeater', $args['type']);
        static::assertSame(FontCatalog::UPLOADED_FONTS_SETTING, $args['settings']);
        static::assertSame('municipio_customizer_section_uploaded_fonts', $args['section']);
        static::assertSame('Add font', $args['button_label']);
        static::assertSame('text', $args['row_label']['type']);
        static::assertSame('Font', $args['row_label']['value']);
        static::assertArrayNotHasKey('name', $args['fields']);
        static::assertArrayHasKey('file', $args['fields']);
    }

    #[TestDox('addHooks() registers styleguide font family options filter')]
    public function testAddHooksRegistersStyleguideFontFamilyOptionsFilter(): void
    {
        $wpService = new FakeWpService([
            'addFilter' => true,
            'addAction' => true,
        ]);

        $fontRepository = $this->createMock(FontRepository::class);
        $googleFontsCssLocaleFilter = $this->createMock(GoogleFontsCssLocaleFilter::class);
        $googleFontsCssLocaleFilter->expects(static::once())->method('addHooks');
        $provider = $this->createMock(FontStyleguideOptionProvider::class);
        $migrator = $this->createMock(FontCatalogMigrator::class);
        $printer = $this->createMock(UploadedFontFacePrinter::class);

        $fontCatalog = new FontCatalog($wpService, $fontRepository, $googleFontsCssLocaleFilter, $provider, $migrator, $printer);
        $fontCatalog->addHooks();

        static::assertContains(
            'Municipio/Styleguide/Customize/TokenData/FontFamilies',
            array_column($wpService->methodCalls['addFilter'], 0),
        );
    }

    #[TestDox('addStyleguideFontFamilies() delegates to the styleguide option provider')]
    public function testAddStyleguideFontFamiliesDelegatesToStyleguideOptionProvider(): void
    {
        $wpService = new FakeWpService();
        $options = [['value' => 'Arial, sans-serif', 'label' => 'Arial']];
        $expectedOptions = [['value' => '"Roboto", sans-serif', 'label' => 'Roboto']];

        $fontRepository = $this->createMock(FontRepository::class);
        $googleFontsCssLocaleFilter = $this->createMock(GoogleFontsCssLocaleFilter::class);
        $provider = $this->createMock(FontStyleguideOptionProvider::class);
        $provider->expects(static::once())->method('addFontFamilies')->with($options)->willReturn($expectedOptions);
        $migrator = $this->createMock(FontCatalogMigrator::class);
        $printer = $this->createMock(UploadedFontFacePrinter::class);

        $fontCatalog = new FontCatalog($wpService, $fontRepository, $googleFontsCssLocaleFilter, $provider, $migrator, $printer);

        static::assertSame($expectedOptions, $fontCatalog->addStyleguideFontFamilies($options));
    }

    #[TestDox('migrateLegacyFonts() delegates to the catalog migrator')]
    public function testMigrateLegacyFontsDelegatesToCatalogMigrator(): void
    {
        $wpService = new FakeWpService();

        $fontRepository = $this->createMock(FontRepository::class);
        $googleFontsCssLocaleFilter = $this->createMock(GoogleFontsCssLocaleFilter::class);
        $provider = $this->createMock(FontStyleguideOptionProvider::class);
        $migrator = $this->createMock(FontCatalogMigrator::class);
        $migrator->expects(static::once())->method('migrate');
        $printer = $this->createMock(UploadedFontFacePrinter::class);

        $fontCatalog = new FontCatalog($wpService, $fontRepository, $googleFontsCssLocaleFilter, $provider, $migrator, $printer);

        $fontCatalog->migrateLegacyFonts();
    }

    #[TestDox('printFontDeclarations() delegates to the uploaded font face printer')]
    public function testPrintFontDeclarationsDelegatesToUploadedFontFacePrinter(): void
    {
        $wpService = new FakeWpService();

        $fontRepository = $this->createMock(FontRepository::class);
        $googleFontsCssLocaleFilter = $this->createMock(GoogleFontsCssLocaleFilter::class);
        $provider = $this->createMock(FontStyleguideOptionProvider::class);
        $migrator = $this->createMock(FontCatalogMigrator::class);
        $printer = $this->createMock(UploadedFontFacePrinter::class);
        $printer->expects(static::once())->method('printDeclarations');

        $fontCatalog = new FontCatalog($wpService, $fontRepository, $googleFontsCssLocaleFilter, $provider, $migrator, $printer);

        $fontCatalog->printFontDeclarations();
    }
}
