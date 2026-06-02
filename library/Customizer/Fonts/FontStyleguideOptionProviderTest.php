<?php

declare(strict_types=1);

namespace Municipio\Customizer\Fonts;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

/**
 * Tests managed font styleguide options.
 */
class FontStyleguideOptionProviderTest extends TestCase
{
    #[TestDox('addFontFamilies() appends uploaded fonts and deduplicates native library matches')]
    public function testAddFontFamiliesAppendsUploadedFontsAndDeduplicatesNativeLibraryMatches(): void
    {
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

        $nativeFontLibraryRepository = $this->createMock(NativeFontLibraryRepository::class);
        $nativeFontLibraryRepository
            ->method('getFontFamilies')
            ->willReturn(['Open Sans', 'Merriweather']);

        $provider = new FontStyleguideOptionProvider($fontRepository, $nativeFontLibraryRepository);

        $options = $provider->addFontFamilies([
            ['value' => 'Arial, sans-serif', 'label' => 'Arial'],
        ]);

        static::assertContains(
            ['value' => '"Open Sans", sans-serif', 'label' => 'Open Sans'],
            $options,
        );
        static::assertContains(
            ['value' => '"Inter", sans-serif', 'label' => 'Inter'],
            $options,
        );
        static::assertContains(
            ['value' => '"Merriweather", sans-serif', 'label' => 'Merriweather'],
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
        $fontRepository = $this->createMock(FontRepository::class);
        $fontRepository->method('getUploadedFonts')->willReturn([]);

        $nativeFontLibraryRepository = $this->createMock(NativeFontLibraryRepository::class);
        $nativeFontLibraryRepository->method('getFontFamilies')->willReturn(['Roboto']);

        $provider = new FontStyleguideOptionProvider($fontRepository, $nativeFontLibraryRepository);

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
