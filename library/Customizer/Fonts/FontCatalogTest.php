<?php

declare(strict_types=1);

namespace Kirki;

if (!class_exists(GoogleFonts::class, false)) {
    /**
     * Test stub for Google font choice retrieval.
     */
    final class GoogleFonts
    {
        /**
         * @param array<string, mixed> $args
         *
         * @return array<int, string>
         */
        public function get_google_fonts_by_args($args = []): array
        {
            return ['Roboto', 'Arimo'];
        }

        /**
         * @return array<string, array{variants?: array<mixed>}>
         */
        public function get_google_fonts(): array
        {
            return [
                'Roboto' => [
                    'variants' => ['regular', '700italic'],
                ],
                'Open Sans' => [
                    'variants' => ['regular', '700'],
                ],
                'Arimo' => [
                    'variants' => ['regular'],
                ],
            ];
        }
    }
}

namespace Municipio\Customizer\Fonts;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

/**
 * Tests native font library integration hooks.
 */
class FontCatalogTest extends TestCase
{
    #[TestDox('addHooks() registers styleguide font family options filter')]
    public function testAddHooksRegistersStyleguideFontFamilyOptionsFilter(): void
    {
        $wpService = new FakeWpService([
            'addFilter' => true,
        ]);

        $fontRepository = $this->createMock(FontRepository::class);
        $provider = $this->createMock(FontStyleguideOptionProvider::class);

        $fontCatalog = new FontCatalog($wpService, $fontRepository, $provider);
        $fontCatalog->addHooks();

        static::assertContains(
            'Municipio/Styleguide/Customize/TokenData/FontFamilies',
            array_column($wpService->methodCalls['addFilter'], 0),
        );
        static::assertArrayNotHasKey('addAction', $wpService->methodCalls);
    }

    #[TestDox('addStyleguideFontFamilies() delegates to the styleguide option provider')]
    public function testAddStyleguideFontFamiliesDelegatesToStyleguideOptionProvider(): void
    {
        $wpService = new FakeWpService();
        $options = [['value' => 'Arial, sans-serif', 'label' => 'Arial']];
        $expectedOptions = [['value' => '"Roboto", sans-serif', 'label' => 'Roboto']];

        $fontRepository = $this->createMock(FontRepository::class);
        $provider = $this->createMock(FontStyleguideOptionProvider::class);
        $provider->expects(static::once())->method('addFontFamilies')->with($options)->willReturn($expectedOptions);

        $fontCatalog = new FontCatalog($wpService, $fontRepository, $provider);

        static::assertSame($expectedOptions, $fontCatalog->addStyleguideFontFamilies($options));
    }
}
