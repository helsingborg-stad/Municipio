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

        $googleFontsCssLocaleFilter = $this->createMock(GoogleFontsCssLocaleFilter::class);
        $googleFontsCssLocaleFilter->expects(static::once())->method('addHooks');

        $fontCatalog = new FontCatalog($wpService, null, null, $googleFontsCssLocaleFilter);
        $fontCatalog->addHooks();

        static::assertContains(
            'Municipio/Styleguide/Customize/TokenData/FontFamilies',
            array_column($wpService->methodCalls['addFilter'], 0),
        );
    }

    #[TestDox('addStyleguideFontFamilies() appends managed Google and uploaded fonts')]
    public function testAddStyleguideFontFamiliesAppendsManagedGoogleAndUploadedFonts(): void
    {
        $wpService = new FakeWpService([
            'getThemeMod' => static fn(string $key, mixed $default): mixed => $key === FontCatalog::GOOGLE_FONTS_SETTING ? ['Roboto', 'Open Sans', 'Roboto', ''] : $default,
        ]);

        $fontRepository = $this->createMock(FontRepository::class);
        $fontRepository
            ->method('getUploadedFonts')
            ->willReturn([
                'Inter' => [
                    'id' => 10,
                    'name' => 'Inter',
                    'type' => 'woff2',
                    'url' => 'https://example.com/inter.woff2',
                ],
                'Open Sans' => [
                    'id' => 11,
                    'name' => 'Open Sans',
                    'type' => 'woff2',
                    'url' => 'https://example.com/open-sans.woff2',
                ],
            ]);

        $fontCatalog = new FontCatalog($wpService, null, $fontRepository);

        $options = $fontCatalog->addStyleguideFontFamilies([
            ['value' => 'Arial, sans-serif', 'label' => 'Arial'],
        ]);

        static::assertContains(
            ['value' => '"Roboto", sans-serif', 'label' => 'Roboto'],
            $options,
        );
        static::assertContains(
            ['value' => '"Open Sans", sans-serif', 'label' => 'Open Sans'],
            $options,
        );
        static::assertContains(
            ['value' => '"Inter", sans-serif', 'label' => 'Inter'],
            $options,
        );

        $openSansMatches = array_values(array_filter(
            $options,
            static fn(array $option): bool => $option['value'] === '"Open Sans", sans-serif',
        ));

        static::assertCount(1, $openSansMatches);
    }

    #[TestDox('printFontDeclarations() does not output Google Fonts CDN links')]
    public function testPrintFontDeclarationsDoesNotOutputGoogleFontsCdnLinks(): void
    {
        $wpService = new FakeWpService([
            'escAttr' => static fn(string $value): string => $value,
            'escUrl' => static fn(string $value): string => $value,
            'wpStripAllTags' => static fn(string $value): string => $value,
        ]);

        $fontRepository = $this->createMock(FontRepository::class);
        $fontRepository->method('getUploadedFonts')->willReturn([]);

        $fontCatalog = new FontCatalog($wpService, null, $fontRepository);

        ob_start();
        $fontCatalog->printFontDeclarations();
        $output = (string) ob_get_clean();

        static::assertSame('', $output);
        static::assertStringNotContainsString('fonts.googleapis.com', $output);
        static::assertStringNotContainsString('id="municipio-google-fonts"', $output);
    }

    #[TestDox('printFontDeclarations() renders uploaded fonts as local @font-face rules')]
    public function testPrintFontDeclarationsRendersUploadedFontsAsLocalFontFaceRules(): void
    {
        $wpService = new FakeWpService([
            'escAttr' => static fn(string $value): string => $value,
            'escUrl' => static fn(string $value): string => $value,
            'wpStripAllTags' => static fn(string $value): string => $value,
        ]);

        $fontRepository = $this->createMock(FontRepository::class);
        $fontRepository
            ->method('getUploadedFonts')
            ->willReturn([
                'Inter' => [
                    'id' => 10,
                    'name' => 'Inter',
                    'type' => 'woff2',
                    'url' => 'https://example.com/inter.woff2',
                ],
            ]);

        $fontCatalog = new FontCatalog($wpService, null, $fontRepository);

        ob_start();
        $fontCatalog->printFontDeclarations();
        $output = (string) ob_get_clean();

        static::assertStringContainsString('id="municipio-uploaded-fonts"', $output);
        static::assertStringContainsString('@font-face', $output);
        static::assertStringContainsString('font-family:"Inter"', $output);
        static::assertStringContainsString('https://example.com/inter.woff2', $output);
        static::assertStringNotContainsString('fonts.googleapis.com', $output);
        static::assertStringNotContainsString('id="municipio-google-fonts"', $output);
    }
}
