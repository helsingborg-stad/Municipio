<?php

declare(strict_types=1);

namespace Municipio\Customizer\Fonts;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

/**
 * Tests managed font styleguide options.
 */
class FontStyleguideOptionProviderTest extends TestCase
{
    #[TestDox('addFontFamilies() appends managed Google and uploaded fonts')]
    public function testAddFontFamiliesAppendsManagedGoogleAndUploadedFonts(): void
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

        $provider = new FontStyleguideOptionProvider($wpService, $fontRepository);

        $options = $provider->addFontFamilies([
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

    #[TestDox('addFontFamilies() ignores malformed option entries')]
    public function testAddFontFamiliesIgnoresMalformedOptionEntries(): void
    {
        $wpService = new FakeWpService([
            'getThemeMod' => static fn(string $key, mixed $default): mixed => $key === FontCatalog::GOOGLE_FONTS_SETTING ? ['Roboto'] : $default,
        ]);

        $fontRepository = $this->createMock(FontRepository::class);
        $fontRepository->method('getUploadedFonts')->willReturn([]);

        $provider = new FontStyleguideOptionProvider($wpService, $fontRepository);

        $options = $provider->addFontFamilies([
            ['value' => '', 'label' => ''],
            ['label' => 'Missing Value'],
            ['value' => 'Arial, sans-serif', 'label' => 'Arial'],
        ]);

        static::assertSame(
            [
                ['value' => 'Arial, sans-serif', 'label' => 'Arial'],
                ['value' => '"Roboto", sans-serif', 'label' => 'Roboto'],
            ],
            $options,
        );
    }
}
